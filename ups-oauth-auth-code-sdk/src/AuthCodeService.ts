import { AuthCodeConstants } from './AuthCodeConstants';
import { LoginInfo } from './LoginInfo';
import { IAuthCodeService, UpsOauthResponse } from './IAuthCodeService';
import { TokenInfo } from './TokenInfo';

export class AuthCodeService implements IAuthCodeService {   
    private baseUrl: string;
    private fetchApi: typeof fetch;

    constructor(fetchHttp: typeof fetch) {
        this.baseUrl = AuthCodeConstants.BASE_URL;
        this.fetchApi = this.validateFetch(fetchHttp);
    }    

    async login(queryParams: Record<string, any> = {}): Promise<UpsOauthResponse<LoginInfo>> {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), AuthCodeConstants.PostTimeout);
        try {            
            const url = this.buildUrlWithQueryParams(`${this.baseUrl}/authorize`, queryParams);
            const httpHeaders = {
                'Content-Type': 'application/x-www-form-urlencoded'
            };
            
            const response = await this.fetchApi(url, {
                method: 'GET',
                headers: httpHeaders
            });
            clearTimeout(timeoutId);    
            if (response.status === 200) {
                const redirectUrl = response.url;
                return this.loginResponse(redirectUrl);
            } else {
                const resp = await response.json();
                if (resp?.response?.errors) {
                    return this.loginErrorResponse(resp);
                }
                const res = JSON.parse(AuthCodeConstants.InternalServerError);
                return this.loginErrorResponse(res);
            }
        } catch (error) {
            clearTimeout(timeoutId);
            if (error.name === 'AbortError') {
                const res = JSON.parse(AuthCodeConstants.TimedOut);
                return this.loginErrorResponse(res);
            } else {
                const res = JSON.parse(AuthCodeConstants.InternalServerError);
                return this.loginErrorResponse(res);
            }
        }
    }

    public async getAccessToken(clientId: string, clientSecret: string, redirectUri: string, authCode: string): Promise<UpsOauthResponse<TokenInfo>> {
        try {
            const body = new URLSearchParams({
                grant_type: 'authorization_code',
                redirect_uri: redirectUri,
                code: authCode
            });
            return await this.postForTokenInfo(AuthCodeConstants.TokenUrl, body, clientId, clientSecret);
        } catch (error) {
            if (error.name === 'AbortError') {
                const res = JSON.parse(AuthCodeConstants.TimedOut);
                return this.apiErrorResponse(res);
            } else {
                const res = JSON.parse(AuthCodeConstants.InternalServerError);
                return this.apiErrorResponse(res);
            }
        }
    }
    
    public async getAccessTokenFromRefreshToken(clientId: string, clientSecret: string, refreshToken: string): Promise<UpsOauthResponse<TokenInfo>> {
        try {
            const body = new URLSearchParams({
                grant_type: 'refresh_token',
                refresh_token: refreshToken
            });
            return await this.postForTokenInfo(AuthCodeConstants.RefreshTokenUrl, body, clientId, clientSecret);
        } catch (error) {
            if (error.name === 'AbortError') {
                const res = JSON.parse(AuthCodeConstants.TimedOut);
                return this.apiErrorResponse(res);
            } else {
                const res = JSON.parse(AuthCodeConstants.InternalServerError);
                return this.apiErrorResponse(res);
            }
        }
    }
    
    private async postForTokenInfo(url: string, body: URLSearchParams, clientId: string, clientSecret: string): Promise<UpsOauthResponse<TokenInfo>> {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), AuthCodeConstants.PostTimeout);
    
        try {
            const response = await this.fetchApi(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': 'Basic ' + btoa(`${clientId}:${clientSecret}`)
                },
                body: body.toString(),
                signal: controller.signal
            });            
            clearTimeout(timeoutId);
            const json = await response.json();
            if (response.status === 200) {
                return this.apiResponse(json);
            }    
            return this.apiErrorResponse(json);
        } catch (error) {
            clearTimeout(timeoutId);
            if (error.name === 'AbortError') {
                const res = JSON.parse(AuthCodeConstants.TimedOut);
                return this.apiErrorResponse(res);
            } else {
                const res = JSON.parse(AuthCodeConstants.InternalServerError);
                return this.apiErrorResponse(res);
            }
        }
    }
    

    private apiResponse(json: any): UpsOauthResponse<TokenInfo> {
        const token: TokenInfo = {
            accessToken: json.access_token,
            clientId: json.client_id,
            expiresIn: json.expires_in,
            issuedAt: json.issued_at,
            refreshToken: json.refresh_token,
            refreshTokenExpiresIn: json.refresh_token_expires_in,
            refreshTokenIssuedAt: json.refresh_token_issued_at,
            refreshTokenStatus: json.refresh_token_status,
            status: json.status,
            tokenType: json.token_type
        };
        return {
            response: token,
            error: null
        };
    }
    
    private apiErrorResponse(json: any): UpsOauthResponse<TokenInfo> {
        const errorResponse = JSON.parse(JSON.stringify(json.response.errors));        
        return {
            response: null,
            error: errorResponse
        };
    }

    private buildUrlWithQueryParams(baseUrl: string, queryParams: Record<string, any>): string {
        const queryString = new URLSearchParams(queryParams).toString();
        return queryString ? `${baseUrl}?${queryString}` : baseUrl;
    }

    private loginResponse(redirectUri: string): UpsOauthResponse<LoginInfo> {
        const loginInfo = new LoginInfo(redirectUri);
        return { response: loginInfo, error: null };
    }

    private loginErrorResponse(jsonData: any): UpsOauthResponse<LoginInfo> {
        const errorResponse = JSON.parse(JSON.stringify(jsonData.response.errors));
        return { response: null, error: errorResponse };
    }

    private validateFetch(fetchHttp: any): typeof fetch {
        if (typeof fetchHttp !== 'function' || !('prototype' in fetchHttp)) {
            return fetch;
        }
        return fetchHttp;    
    }    
}

