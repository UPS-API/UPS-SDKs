export class TokenInfo {
    issuedAt: string;
    tokenType: string;
    clientId: string;
    accessToken: string;
    expiresIn: string;
    status: string;

    constructor(
        issuedAt: string,
        tokenType: string,
        clientId: string,
        accessToken: string,
        expiresIn: string,
        status: string
    ) {
        this.issuedAt = issuedAt;
        this.tokenType = tokenType;
        this.clientId = clientId;
        this.accessToken = accessToken;
        this.expiresIn = expiresIn;
        this.status = status;
    }
}