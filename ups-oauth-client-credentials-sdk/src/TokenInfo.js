"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.TokenInfo = void 0;
var TokenInfo = /** @class */ (function () {
    function TokenInfo(issuedAt, tokenType, clientId, accessToken, expiresIn, status) {
        this.issuedAt = issuedAt;
        this.tokenType = tokenType;
        this.clientId = clientId;
        this.accessToken = accessToken;
        this.expiresIn = expiresIn;
        this.status = status;
    }
    return TokenInfo;
}());
exports.TokenInfo = TokenInfo;
