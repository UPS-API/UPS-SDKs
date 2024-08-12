package authcode

import ( 
    "io"
    "net/http"
    "net/url"
    "strings"
    "time"
    "encoding/json"
    "net"
)

const (
    tokenUrl              = "https://onlinetools.ups.com/security/v1/oauth/token"
    refreshTokenUrl       = "https://onlinetools.ups.com/security/v1/oauth/refresh"
    loginUrl              = "https://onlinetools.ups.com/security/v1/oauth/authorize"
    timeoutDuration       = 30 * time.Second
    tlsHandshakeTimeout   = 10 * time.Second
    idleConnTimeout       = 10 * time.Second
    responseHeaderTimeout = 10 * time.Second
    expectContinueTimeout = 10 * time.Second
    timedOut = `{"response":{"errors":[{"code":"10500","message":"Request Timed out."}]}}`
    internalServerError = `{"response":{"errors":[{"code":"10500","message":"Internal server error"}]}}`
)

func setHttpClientTimeouts(httpClient *http.Client) *http.Client {
    if httpClient == nil {
        return &http.Client {
            Timeout: timeoutDuration,
            Transport: &http.Transport {
                TLSHandshakeTimeout: tlsHandshakeTimeout,
                IdleConnTimeout: idleConnTimeout,
                ResponseHeaderTimeout: responseHeaderTimeout,
                ExpectContinueTimeout: expectContinueTimeout,
            },
        }
    }

    httpClient.Timeout = timeoutDuration

    if transport, ok := httpClient.Transport.(*http.Transport); ok {
        transport.TLSHandshakeTimeout = tlsHandshakeTimeout
        transport.IdleConnTimeout = idleConnTimeout
        transport.ResponseHeaderTimeout = responseHeaderTimeout
        transport.ExpectContinueTimeout = expectContinueTimeout
    } else {
        httpClient.Transport = &http.Transport {
            TLSHandshakeTimeout: tlsHandshakeTimeout,
            IdleConnTimeout: idleConnTimeout,
            ResponseHeaderTimeout: responseHeaderTimeout,
            ExpectContinueTimeout: expectContinueTimeout,
        }
    }
    return httpClient
}

func Login(httpClient *http.Client, queryParams map[string]string ) (UpsOauthResponse) {
    var _httpClient *http.Client = setHttpClientTimeouts(httpClient)
        
    urlWithParams, err := url.Parse(loginUrl)
    if err != nil {
        return apiErrorResponse(err.Error())
    }

    query := urlWithParams.Query()
    for key, value := range queryParams {
        query.Set(key, value)
    }
    urlWithParams.RawQuery = query.Encode()

    req,err := http.NewRequest(http.MethodGet, urlWithParams.String(), nil)
    if err != nil {
        return apiErrorResponse(err.Error())
    }
        
    req.Header.Set("Content-Type", "application/x-www-form-urlencoded")    
    res, err := _httpClient.Do(req)
    if err != nil {
        if netErr, ok := err.(net.Error); ok && netErr.Timeout() {
            return apiErrorResponse(timedOut)
        }
        return apiErrorResponse(err.Error())
    }

    defer res.Body.Close()    

    if !(res.StatusCode >= 200 && res.StatusCode <= 299) {
        response, err := io.ReadAll(res.Body)
        if err != nil {
            return apiErrorResponse(internalServerError)
        }
        return apiErrorResponse(string(response))
    }

    redirectUrl := res.Request.URL.String()

    redirectResponse := TokenInfo{
        RedirectURI: redirectUrl,
    }

    upsResponse := UpsOauthResponse{
        Response: redirectResponse,
        Error: "",
    }
    
    return upsResponse
}

func GetAccessToken(httpClient *http.Client, clientId string, clientSecret string, redirectUri string, authCode string) (UpsOauthResponse) {
    var _httpClient *http.Client = setHttpClientTimeouts(httpClient)   

    body := url.Values{}
    body.Set("grant_type", "authorization_code")
    body.Set("redirect_uri", redirectUri)
    body.Set("code", authCode)

    encodedData := body.Encode()

    req,err := http.NewRequest(http.MethodPost, tokenUrl, strings.NewReader(encodedData))
    if err != nil {        
        return apiErrorResponse(err.Error())
    }

    req.SetBasicAuth(clientId, clientSecret)
    req.Header.Set("Content-Type", "application/x-www-form-urlencoded")   

    res, err := _httpClient.Do(req)
    if err != nil {  
        if netErr, ok := err.(net.Error); ok && netErr.Timeout() {
            return apiErrorResponse(timedOut)
        }
        return apiErrorResponse(err.Error())
    }

    defer res.Body.Close()
    
    response, err := io.ReadAll(res.Body)    
    if err != nil {
        return apiErrorResponse(internalServerError)
    }
    respString := string(response)
    var data TokenInfo
    errr := json.Unmarshal([]byte(respString), &data)
    if errr != nil {
        return apiErrorResponse(internalServerError)
    } 
    
    if !(res.StatusCode >= 200 && res.StatusCode <= 299) {
        return apiErrorResponse(string(response))
    }
    

    upsResponse := UpsOauthResponse{
        Response: data,
        Error: "",
    }
    return upsResponse
}

func GetAccessTokenFromRefreshToken(httpClient *http.Client, clientId string, clientSecret string, refreshToken string) (UpsOauthResponse) {
    var _httpClient *http.Client = setHttpClientTimeouts(httpClient)   
    
    if httpClient == nil {
        _httpClient = &http.Client {
            Timeout: timeoutDuration,
            Transport: &http.Transport {
                TLSHandshakeTimeout: tlsHandshakeTimeout,
                IdleConnTimeout: idleConnTimeout,
                ResponseHeaderTimeout: responseHeaderTimeout,
                ExpectContinueTimeout: expectContinueTimeout,
            },
        }

    } else {
        _httpClient = httpClient
    }

    body := url.Values{}
    body.Set("grant_type", "refresh_token")
    body.Set("refresh_token", refreshToken)

    encodedData := body.Encode()

    req,err := http.NewRequest(http.MethodPost, refreshTokenUrl, strings.NewReader(encodedData))
    if err != nil {        
        return apiErrorResponse(internalServerError)
    }

    req.SetBasicAuth(clientId, clientSecret)
    req.Header.Set("Content-Type", "application/x-www-form-urlencoded")   

    res, err := _httpClient.Do(req)
    if err != nil {        
        if netErr, ok := err.(net.Error); ok && netErr.Timeout() {
            return apiErrorResponse(timedOut)
        }
        return apiErrorResponse(err.Error())
    }

    defer res.Body.Close()

    response, err := io.ReadAll(res.Body)

    if err != nil {        
        return apiErrorResponse(internalServerError)
    }
    
    respString := string(response)
    var data TokenInfo
    errr := json.Unmarshal([]byte(respString), &data)
    if errr != nil {
        return apiErrorResponse(internalServerError)
    } 

    if !(res.StatusCode >= 200 && res.StatusCode <= 299) {
        return apiErrorResponse(respString)
    }

    upsResponse := UpsOauthResponse{
        Response: data,
        Error: "",
    }

    return upsResponse
}

func apiErrorResponse(errorResponse string) UpsOauthResponse {    
    response := UpsOauthResponse{
        Response: TokenInfo{},
        Error: errorResponse,
    } 
    return response
}

type TokenInfo struct {
    Issued_at                string `json:"issued_at"`
    Token_type               string `json:"token_type"`
    Client_id                string `json:"client_id"`
    Access_token             string `json:"access_token"`
    Refresh_token            string `json:"refresh_token"`
    Refresh_token_issued_at  string `json:"refresh_token_issued_at"`
    Refresh_token_expires_in string `json:"refresh_token_expires_in"`
    Refresh_token_status     string `json:"refresh_token_status"`
    Expires_in               string `json:"expires_in"`
    Status                   string `json:"status"`
    RedirectURI              string `json:"redirect_uri"`
}


type LoginInfo struct {
     RedirectURL string `json:"redirect_url"`
}

type UpsOauthResponse struct {
	Response TokenInfo
	Error string
}
