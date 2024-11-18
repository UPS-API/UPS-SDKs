# UPS-API SDKs for NodeJs

###### Table of Contents
[Overview](#overview)<br>
[Available SDKs](#available-sdks)<br>
[Prerequisistes](#prerequisiites)<br>
[OAuth Using Client Credentials](#oauth-using-client-credentials)<br>
[OAuth Using Auth Code](#oauth-using-auth-code)<br>
[Response Specifications](#response-specifications)<br>

### Overview
UPS provides an SDK to help generate and refresh OAuth Tokens which are needed when consuming UPS APIs.

### Available SDKs
- Create OAuth Token using Client Credentials. [Get the npm Package.](https://github.com/UPS-API/UPS-SDKs/tree/Node)
- Create OAuth Token using an Auth Code and SSO. [Get the npm Package.](https://github.com/UPS-API/UPS-SDKs/tree/Node)

### Prerequisites
Before you can use the SDK to create an access token, ensure you have:
- A UPS developer account. [Get one now!](https://developer.ups.com/)
- A valid Client ID and Client Secret.
- GitHub Packages as a package source for Node Package Manager. *This package is not available from npmjs. For more information look [here](https://docs.github.com/en/packages/working-with-a-github-packages-registry/working-with-the-npm-registry)
***

# OAuth Using Client Credentials

### About
Create an OAuth Token using Client Credentials

### Installation
Install from `package.json`:

``` Javascript
"@ups-api/oauth-client-credentials-sdk": "1.0.0"
```


### Definition
```Typescript
export class ClientCredentialService
```

### Constructors
| Definition | Description |
|------------|-------------|
| ClientCredentialService(fetchHttp: typeof fetch) | Initializes a new instance of the ClientCredentialService class. |

### Methods
| Definition | Description |
|------------|-------------|
| getAccessToken(clientId : string, clientSecret : string) | Returns a promise using only the provided id and secret |
| getAccessToken(clientId : string, clientSecret : string, headers = null) | Returns a promise using the provided id, secret, and additional optional request headers. |
| getAccessToken(clientId : string, clientSecret : string, null, customClaims = null)| Returns a promise using the provided id, secret, and additional request body properties|
| getAccessToken(clientId : string, clientSecret : string, headers = null, customClaims = null) | Returns a promise using the provided id, secret, additional request headers, and additional requesty body properties |

### Example

#### Injecting ClientCredentialService
```NodeJs
const { ClientCredentialService } = require('@ups-api/client-credentials-sdk');

const clientCredentialService = new ClientCredentialService(fetch);

```
#### Initializing Variables
```NodeJs
// Get ClientID and Secret from secure credential vault.
let clientId  = "YOUR_CLIENT_ID";
let clientSecret  = "YOUR_CLIENT_SECRET";

// Create variable for optional headers
const exampleHeader = { "headerkey": "headerValue" };

// Create variable for optional post body
const exampleBody = { "bodyKey": "bodyValue" };
```

#### Creating A Token
```NodeJs
const clientCredentialService = new ClientCredentialService(fetch);
clientCredentialService.getAccessToken(clientId, clientSecret, null, customClaims).then(function (response) {
    res.render(response?.response?.accessToken);
});

```

***

## Response Specifications

### Overview
Consuming an SDK will yield an `UpsOauthResponse`. This response object can contain a `TokenInfo` object or an `ErrorResponse` object.

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```Typescript
export class TokenInfo
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| issuedAt | string | Issue time of requested token in milliseconds. |
| tokenType | string | Type of requested access token. |
| clientId | string | Client id for requested token. |
| accessToken | string | Token to be used in API requests. |
| expiresIn | string | Expire time for requested token in seconds. |
| status | string | Status for requested token. |

### UpsOauthResponse Class

#### About
This class serves as a container for OAuth authentication response object.

#### Definition
```Typescript
public class UpsOauthResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| response | TokenInfo | Provides access to information about the token. |
| error | ErrorResponse | Contains a list of errors from an unsuccessful API call. |

### ErrorResponse Class

#### About
This class contains a list of errors that occurred when attempting to create an access token.

#### Definition
```Typescript
export class ErrorResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| errors | List<ErrorModel> | A list of errors. |

### ErrorModel Class

#### About
This class contains an error code and message based on the response during access token creation.

#### Definition
```Typescript
export class ErrorModel
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| code | string | A code representation of the error that occurred. |
| message | string | A message describing the error that occurred. |


***

# OAuth Using Auth Code

### About
Create an OAuth Token using an Auth Code.

### Installation
Install from `package.json`:

```
"@ups-api/oauth-authcode-sdk": "1.0.0"
```

***

## AuthCodeService Class

### Definition
```Typescript
export class AuthCodeService
```

### Constructors
| Definition | Description |
|------------|-------------|
| AuthCodeService(fetch) | Initializes a new instance of the `AuthCodeService` class. Requires a fetch function as a parameter.

### Methods
| Definition | Description |
|------------|-------------|
| login(queryParams : Record<string, any> = {}) | Initiates the OAuth login flow by redirecting the user to the UPS authorization page. Returns a `Promise` containing `LoginInfo` which contains the Redirect URI as a string. |
| getAccessToken(clientId: string, clientSecret: string, redirectUri: string, authCode: string) | Returns a `Promise` containing a `TokenInfo` object when successful. Requires a Client ID, Client Secret, Redirect URI, and an Auth Code |
| getAccessTokenFromRefreshToken(clientId: string, clientSecret: string, refreshToken: string) | Returns a `Promise` containing a `TokenInfo` object. Requires a Client ID, Client Secret, and a Redirect URL. |

***

## Example

#### Injecting AuthCodeService
```NodeJs
//Inject AuthCodeService into your javascript file.

const { AuthCodeService } = require('@ups-api/authcode-sdk');
```
#### Initialize Variables
```NodeJs
//Create variables to store Access and Refresh Tokens
let accessToken = string.Empty;
let refreshToken = string.Empty;

// Get ClientID and Secret from secure credential vault.
let clientId  = "YOUR_CLIENT_ID";
let clientSecret  = "YOUR_CLIENT_SECRET";

// Initialize redirect uri
let redirectUri = "YOUR_REDIRECT_URI";
```

#### Logging In
```NodeJs
//Create and add query parameters to request.
const queryParams = {
    client_id: 'clientId',
    redirect_uri: 'redirectUri',
    response_type: 'code'
};

//Log in
 authCodeService.login(queryParams).then(function (data) {     
     res.redirect(data.response.redirectUri);            
 });
```

#### Creating A Token
```NodeJs
//Get Auth Code from request query string.
const code = req.query.code;
if (code){
authCodeService.getAccessToken(clientId, clientSecret, redirectUri, authCode).then(function (data) {    
    res.render(data.response.accessToken);
});}
```

#### Refreshing A Token
```NodeJs
//Use existing Refresh Token and credentials to get a new token.
authCodeService.getAccessTokenFromRefreshToken(clientId, clientSecret, refreshToken).then(function (data) {
    res.render(data.response.refreshToken);
});
```

***

## Response Specifications

### Overview
Consuming an SDK will yield an `UpsOauthResponse`. This response object can contain a `TokenInfo` object, an `ErrorResponse` object or a `LoginInfo` object.

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```Typescript
export class TokenInfo
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| issuedAt | string | Issue time of requested token in milliseconds. |
| tokenType | string | Type of requested access token. |
| clientId | string | Client id for requested token. |
| accessToken | string | Token to be used in API requests. |
| expiresIn | string | Expire time for requested token in seconds. |
| status | string | Status for requested token. |
| refreshToken | string | Refresh token to be used to refresh existing token. |
| refreshTokenExpiresIn | String | Expire time for refresh token in seconds. |
| refreshTokenStatus | String | Status for refresh token. |
| refreshTokenIssuedAt | String | Time refresh token was issued. |


### UpsOauthResponse Class

#### About
This class serves as a container for OAuth authentication response object.

#### Definition
```Typescript
public class UpsOauthResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| response | T | Provides access to information about the token or login information. |
| error | ErrorResponse | Contains a list of errors from an unsuccessful API call. |

### ErrorResponse Class

#### About
This class contains a list of errors that occurred when attempting to create an access token.

#### Definition
```Typescript
export class ErrorResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| errors | List<ErrorModel> | A list of errors. |

### ErrorModel Class

#### About
This class contains an error code and message based on the response during access token creation.

#### Definition
```Typescript
export class ErrorModel
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| code | string | A code representation of the error that occurred. |
| message | string | A message describing the error that occurred. |


***
## LoginInfo Class

### Definition
```Typescript
export class LoginInfo
```

### Properties

| Definition | Type | Description |
|------------|-------------|------|
| redirectUri | string | The redirect uri for your application. |

***

# OAuth Error Codes

#### Error Code List
| Error Code | Description |
|------------|-------------|
| 10400 | Indicates an “Invalid/Missing Authorization Header.” This error is more general and can occur at various stages of the OAuth flow. <br> <br> <u>Possible reasons:</u> <br> - Missing or invalid parameters (e.g., missing response_type, client_id, etc.). <br> - Unauthorized Client. The client (your application) is not authorized to request an authorization code. <br> - Unsupported Response Type. The authorization server does not support obtaining an authorization code using the specified method. <br> <br> <u>Solution:</u> <br> Review your request parameters. Make sure your request includes the proper authorization header and ensure compliance with UPS OAuth specifications. <br>|
| 10401 | This error occurs when the “ClientId is invalid.” This error occurs specifically during the client authentication process. It indicates that the client (your application) making the request is not recognized or authorized by the authorization server.<br> <br> <u>Possible reasons:</u> <br> - Incorrect client ID or secret. It could be typographical errors in the client ID or secret. <br> - Unauthorized client (not registered or configured properly). <br> - Mismatched redirect URIs. The URI you provided during authentication maybe doesn’t match any registered redirect URIs.<br> <br>  <u>Solution:</u> <br> Verify your client credentials and ensure proper registration with the authorization server.|
