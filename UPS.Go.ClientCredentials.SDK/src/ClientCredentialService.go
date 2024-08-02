//package clientcredential
package github.com/UPS-API/UPS-SDKs

import (
    "io"
    "net/http"
    "net/url"
    "strings"
    "time"
    "encoding/json"
)

const baseUrl = "https://onlinetools.ups.com/security/v1/oauth/token"

func GetAccessToken(clientId string, clientSecret string, headers map[string]string, customClaims map[string]string) (UpsOauthResponse) {
    httpClient := &http.Client {
        Timeout: time.Second * 30,
        Transport: &http.Transport {
            TLSHandshakeTimeout: time.Second * 10,
            IdleConnTimeout: time.Second * 30,
            ResponseHeaderTimeout: time.Second * 30,
            ExpectContinueTimeout: time.Second * 30,
        },
    }

    body := url.Values{}
    body.Set("grant_type", "client_credentials")
    body.Set("scope", "public")

    for keys := range customClaims {
        claim := "{\"" + keys + "\":\"" + customClaims[keys] + "\"}"
        body.Add("custom_claims", claim)
    }
    encodedData := body.Encode()

    req,err := http.NewRequest(http.MethodPost, baseUrl, strings.NewReader(encodedData))
    if err != nil {        
        return apiErrorResponse(err.Error())
    }

    req.SetBasicAuth(clientId, clientSecret)
    req.Header.Set("Content-Type", "application/x-www-form-urlencoded")

    for keys := range headers {
        req.Header.Set(keys, headers[keys])
    }

    res, err := httpClient.Do(req)
    if err != nil {
        return apiErrorResponse(err.Error())
    }

    defer res.Body.Close()

    response, err := io.ReadAll(res.Body)
    if err != nil {
        return apiErrorResponse(err.Error())
    }

    respString := string(response)
    var data TokenInfo
    errr := json.Unmarshal([]byte(respString), &data)
    if errr != nil {
        return apiErrorResponse(respString)
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
	Issued_at string `json: "issued_at`
	Token_type string `json: "token_type`
	Client_id string `json: "client_id`
	Access_token string `json: "access_token`
	Refresh_token string `json: "refresh_token`
	Refresh_token_issued_at string `json: "refresh_token_issued_at`
	Expires_in string `json: "expires_in`
	Status string `json: "status`
}

type UpsOauthResponse struct {
	Response TokenInfo
	Error string
}