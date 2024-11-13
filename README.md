# UPS-API SDKs for PHP

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
- OAuth Token using **Client Credentials** - This allows the client to directly authorize itself using its client ID and secret.
- OAuth Token using an **Authorization Code flow** - This involves obtaining an authorization code from UPS authorization server, which is then exchanged for an access token. 

### Prerequisites
Before you can utilize UPS OAuth APIs SDK, you must obtain the following: 
- A UPS developer account. [Get one now!](https://developer.ups.com/)
- A valid client ID and client Secret credentials for your application.


***

# OAuth Using Client Credentials
To get an access token using the Client Credentials Flow, follow these steps:

### Installation
`composer require ups-api/php-oauth-client-credentials-sdk`

### ClientCredentialService Class

#### About
This class serves as a container for obtaining an access token using the Client Credentials Flow

### Definition
```PHP
class ClientCredentialService
```

### Constructors
| Definition | Description |
|------------|-------------|
| ClientCredentialService($httpClient) | Initializes a new instance of the ClientCredentialService class with $httpClient instance. |


### Methods
| Definition | Description |
|------------|-------------|
| getAccessToken(String, String, Array , Array) | Returns an access token using the provided client Id, client Secret, headers, bodyparams. |

### Example

#### Creating A Token
```PHP
String $clientId = "YOUR_CLIENT_ID";
String $clientSecret = "YOUR_CLIENT_SECRET";
$headers = array();
$headers["YOUR_HEADER"] = "YOUR_VALUE";

$customClaims = array();
$customClaims["YOUR_CUSTOMCLAIMS"] = "YOUR_VALUE";

$httpClient = new HttpClient();

ClientCredentialService service = new ClientCredentialService($httpClient);
public String ExampleTokenMethod() {
  return service.generateToken(clientId, clientSecret, $headers, $customClaims);
}
```

***

## Response Specifications

### Overview
Consuming an SDK will yield an `UpsOauthResponse`. This response object can contain a `TokenInfo` object or an `ErrorResponse` object.

### UpsOauthResponse Class

#### About
This class serves as a container for OAuth authentication response object.

#### Definition
```PHP
class UpsOauthResponse
```

#### Methods
|Definition | Type | Description |
|-----------|------|-------------|
| getResponse() | TokenInfo | Returns a `TokenInfo` object. |
| setResponse(TokenInfo) | void | Sets the response object. Requires a `TokenInfo` object. |
| getError() | ErrorResponse | Returns the `ErrorResponse` object in the case of failure. |
| setError(ErrorResponse) | void | Sets the `ErrorResponse` object. |

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```PHP
class TokenInfo
```

#### Methods
| Definition | Type | Description |
|------------|-------------|------|
| getIssuedAt() | String | Returns the issue time of requested token in milliseconds. |
| getTokenType() | String | Returns the type of requested access token. |
| getClientId() | String | Returns the client id for requested token. |
| getAccessToken() | String | Returns the token to be used in API requests. |
| getExpiresIn() | String | Returns the expire time for requested token in seconds. |
| getStatus() | String | Returns the status for requested token. |
| setIssuedAt(String) | void | Sets the issue time of requested token in milliseconds. |
| setTokenType(String) | void | Sets the type of requested access token. |
| setClientId(String) | void | Sets the client id for requested token. |
| setAccessToken() | void | Sets the token to be used in API requests. |
| setExpiresIn() | void | Sets the expire time for requested token in seconds. |
| setStatus() | void | Sets the status for requested token. |

### ErrorResponse Class

#### About
This class contains a list of errors that occurred when attempting to create an access token.

#### Definition
```PHP
class ErrorResponse
```

#### Methods
| Definition | Type | Description |
|------------|-------------|------|
| getErrors() | List<ErrorModel> | Returns a list of errors. |
| setErrors(List<ErrorModel>) | void | Sets a list of errors. |

### ErrorModel Class

#### About
This class contains an error code and message based on the response during access token creation.

#### Definition
```PHP
class ErrorModel
```

#### Methods
| Definition | Type | Description |
|------------|-------------|------|
| getCode() | String | Returns a code representation of the error that occurred. |
| getMessage() | String | Returns a message describing the error that occurred. |
| setCode(String) | void | Sets the error code. |
| setMessage(String) | void | Sets the error message. |


# OAuth Using Authorization Code flow
To get an access token using the Authorization code flow, follow these steps:

### Installation
`composer require ups-api/php-oauth-authcode-sdk`
***
## AuthCodeService Class

### Definition
```PHP
class AuthCodeService
```
A built-in class that contains information for authenticating user and then redirecting to the client application's _redirect uri_ which includes the authorization code, allowing the client application to get the access token.

### Constructors
| Definition | Description |
|------------|-------------|
| AuthCodeService(HttpClient) | Initializes a new instance of the `AuthCodeService` class. Requires an `HttpClient` |


### Methods
| Definition | Description |
|------------|-------------|
|login(Array) | Initiates the OAuth login flow by redirecting the user to the UPS authorization page. This method accepts Query params as an Array and returns redirection URL when successful. |
|getAccessToken(String, String, String, String) | Returns an `APIResponse` containing a `TokenInfo` object when successful. Requires a Client ID, Client Secret, Redirect URI, an Auth Code, and any additional headers required. |
|getAccessTokenFromRefreshToken(String, String, String) | Returns an `APIResponse` containing a `TokenInfo` object. Requires a Client ID, Client Secret, a Redirect URL, and any additional headers required. |

### Example

#### Initialize Variables
```PHP
//Create Service object, variables to store Access and Refresh Tokens.
String access_token = "";
String refresh_token = "";

//Create variables for ID and Redirect URI.
String $clientID = "YOUR_CLIENT_ID";
String $redirectUri = "YOUR_REDIRECT_URI";

 $httpClient = new HttpClient();
 $authCodeService = new AuthCodeService($httpClient);
```

#### Logging In
```PHP
//Create and add query parameters to request.
$queryparams = array();
$queryparams["client_id"] = "Your Client Id";
$queryparams["redirect_uri"] = "Your Redirection URL";
$queryparams["response_type"] = "code";

//Login
 $response= $authCodeService->login($queryparams);
```

#### Creating A Token
```PHP
 $response= $authCodeService->get_access_token('Your Client ID'
        ,'Your Client Secret',
        'Your Redirection URL','Code received after successful Login');  
```

#### Refreshing A Token
```PHP
$response= $authCodeService->get_access_token_from_refresh_token('Your Client ID'
        ,'Your Client Secret',
        'Your Redirection URL','Code received after successful Login');  

```

***

## Response Specifications

### Overview
Consuming an SDK will yield an `UpsOauthResponse`. This response object can contain a `TokenInfo` object, `LoginInfo` object or an `ErrorResponse` object.

### UpsOauthResponse Class

#### About
This class serves as a container for OAuth authentication response object.

#### Definition
```PHP
class UpsOauthResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| Response | T | Provides access to information about the token. |
| Error | Contains a list of errors from an unsuccessful API call. |

#### Methods
|Definition | Type | Description |
|-----------|------|-------------|
| to_dict() | Returns the array as response containing "response" and "error". |
| 

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```PHP
class TokenInfo
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| issued_at | String | Issue time of requested token in milliseconds. |
| token_type | String | Type of requested access token. |
| client_id | String | Client id for requested token. |
| access_token | String | Token to be used in API requests. |
| expires_in | String | Expire time for requested token in seconds. |
| status | String | Status for requested token. |
| refresh_token_expires_in | String | Expire time for refresh token in seconds. |
| refresh_token_status | String | Status for refresh token. |
| refresh_token_issued_at | String | Time refresh token was issued. |

### ErrorModel Class

#### About
This class used to set error code and corrosponding error message on the error response during access token creation.

#### Definition
```PHP
class ErrorModel
```

#### Methods
| Definition | Type | Description |
|------------|-------------|------|
| constructor(string, string) | Array | Set the error code and message. |

### LoginInfo Class

#### About
This class contains information about the Redirect URI.

#### Definition
```PHP
class LoginInfo
```

#### Properties
| Definition | Type | Description |
|------------|------|-------------|
| redirectUri | String | The Redirect URI to be used when logging in. |

***

# OAuth Error Codes

#### Error Code List
| Error Code | Description |
|------------|-------------|
| 10400 | Indicates an “Invalid/Missing Authorization Header.” This error is more general and can occur at various stages of the OAuth flow. <br> <br> <u>Possible reasons:</u> <br> - Missing or invalid parameters (e.g., missing response_type, client_id, etc.). <br> - Unauthorized Client. The client (your application) is not authorized to request an authorization code. <br> - Unsupported Response Type. The authorization server does not support obtaining an authorization code using the specified method. <br> <br> <u>Solution:</u> <br> Review your request parameters. Make sure your request includes the proper authorization header and ensure compliance with UPS OAuth specifications. <br>|
| 10401 | This error occurs when the “ClientId is invalid.” This error occurs specifically during the client authentication process. It indicates that the client (your application) making the request is not recognized or authorized by the authorization server.<br> <br> <u>Possible reasons:</u> <br> - Incorrect client ID or secret. It could be typographical errors in the client ID or secret. <br> - Unauthorized client (not registered or configured properly). <br> - Mismatched redirect URIs. The URI you provided during authentication maybe doesn’t match any registered redirect URIs.<br> <br>  <u>Solution:</u> <br> Verify your client credentials and ensure proper registration with the authorization server.|
