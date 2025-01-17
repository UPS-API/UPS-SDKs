export class TokenInfo {
    refreshTokenExpiresIn: string;
    refreshTokenStatus: string;
    issuedAt: string;
    tokenType: string;
    clientId: string;
    accessToken: string;
    refreshToken: string;
    refreshTokenIssuedAt: string;
    expiresIn: string;
    status: string;

    constructor(
        refreshTokenExpiresIn: string,
        refreshTokenStatus: string,
        issuedAt: string,
        tokenType: string,
        clientId: string,
        accessToken: string,
        refreshToken: string,
        refreshTokenIssuedAt: string,
        expiresIn: string,
        status: string
    ) {
        this.refreshTokenExpiresIn = refreshTokenExpiresIn;
        this.refreshTokenStatus = refreshTokenStatus;
        this.issuedAt = issuedAt;
        this.tokenType = tokenType;
        this.clientId = clientId;
        this.accessToken = accessToken;
        this.refreshToken = refreshToken;
        this.refreshTokenIssuedAt = refreshTokenIssuedAt;
        this.expiresIn = expiresIn;
        this.status = status;
    }
}