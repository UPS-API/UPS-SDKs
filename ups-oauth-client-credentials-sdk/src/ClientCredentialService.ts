import { ClientCredentialConstants } from './ClientCredentialConstants';
import { IClientCredentialService, UpsOauthResponse, ErrorResponse } from './IClientCredentialService';
import { TokenInfo } from './TokenInfo';

export class ClientCredentialService implements IClientCredentialService {
    private baseUrl: string;
    private fetchApi: typeof fetch;

    constructor(fetchHttp: typeof fetch) {
        this.baseUrl = ClientCredentialConstants.BASE_URL;
        this.fetchApi = this.validateFetch(fetchHttp);
    }

    public async getAccessToken(clientId, clientSecret, headers = null, customClaims = null): Promise<UpsOauthResponse<TokenInfo>> {
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), ClientCredentialConstants.PostTimeout);
            const authorization = `Basic ${Buffer.from(`${clientId}:${clientSecret}`).toString('base64')}`;
            const httpHeaders = {
                "Content-Type": "application/x-www-form-urlencoded",
                "Authorization": authorization,
                ...headers
            };

            const body: Record<string, string> = {
                grant_type: 'client_credentials',
                scope: 'public',
                ...(customClaims && { custom_claims: JSON.stringify(customClaims) })
            };

            const customBody = new URLSearchParams(body);
            const response = await this.fetchApi(ClientCredentialConstants.TokenUrl, {
                method: 'POST',
                headers: httpHeaders,
                body: customBody,
                signal: controller.signal
            });

            const json = await response.json();
            clearTimeout(timeoutId);
            if (response.status === 200) {
                return this.apiResponse(json);
            }
            return this.apiErrorResponse(await JSON.stringify(json));
        } catch (error) {
            if (error.name === 'AbortError') {
                const res = JSON.parse(ClientCredentialConstants.TimedOut);
                return this.apiErrorResponse(res);
            } else {
                const res = JSON.parse(ClientCredentialConstants.InternalServerError);
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
            status: json.status,
            tokenType: json.token_type
        };
        return {
            response: token,
            error: null
        };
    }

    private apiErrorResponse(json: any): UpsOauthResponse<TokenInfo> {
        const response = json;
        const errorResponse: ErrorResponse = JSON.stringify(response);

        return {
            response: null,
            error: errorResponse
        };
    }

    private validateFetch(fetchHttp: any): typeof fetch {
        if (typeof fetchHttp !== 'function' || !('prototype' in fetchHttp)) {
            return fetch;
        }
        return fetchHttp;
    }
}




