// MyService.ts
export class AuthCodeConstants {
    static readonly AuthorizeUrl: string = 'https://onlinetools.ups.com/security/v1/oauth/authorize';
    static readonly TokenUrl: string = 'https://onlinetools.ups.com/security/v1/oauth/token';
    static readonly RefreshTokenUrl: string = 'https://onlinetools.ups.com/security/v1/oauth/refresh';
    static readonly BASE_URL: string = 'https://onlinetools.ups.com/security/v1/oauth';
    static readonly GetTimeout: number = 5000;
    static readonly PostTimeout: number = 5000;
    static readonly TimedOut: string = '{"response":{"errors":[{"code":"10400","message":"Request timed out."}]}}';
    static readonly InternalServerError: string = '{"response":{"errors":[{"code":"10400","message":"Internal Server error."}]}}';
}