
export interface IClientCredentialService {
    //login(queryParams?: Record<string, string>): Promise<UpsOauthResponse<LoginInfo>>;
    //getAccessToken(clientId: string, clientSecret: string, redirectUri: string, authCode: string): Promise<UpsOauthResponse<TokenInfo>>;
    //getAccessTokenFromRefreshToken(clientId: string, clientSecret: string, refreshToken: string): Promise<UpsOauthResponse<TokenInfo>>;
}

export interface UpsOauthResponse<T> {
    response: T | null;
    error: ErrorResponse | null;
}

export interface ErrorResponse {
    // Define the structure of the error response
}