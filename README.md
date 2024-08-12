# UPS-API OAuth SDKs for Python

###### Table of Contents
[Overview](#overview)<br>
[Available SDKs](#available-sdks)<br>
[Prerequisites](#prerequisites)<br>
[OAuth Using Client Credentials](#oauth-using-client-credentials)<br>
[OAuth Using Auth Code Flow](#oauth-using-auth-code)<br>
[Response Specifications](#response-specifications)<br>

### Overview
UPS provides a SDK that helps in creating and refreshing OAuth tokens required when consuming [UPS APIs](https://developer.ups.com/catalog).

### Available SDKs
- OAuth Token using **Client Credentials** - This allows the client to directly authorize itself using its client ID and secret. [Get the code!](https://github.com/UPS-API/UPS-SDKs/tree/Python/UPS.Python.ClientCredentials.Sdk)
- OAuth Token using an **Authorization Code flow** - This involves obtaining an authorization code from UPS authorization server, which is then exchanged for an access token. [Get the code!](https://github.com/UPS-API/UPS-SDKs/tree/Python/UPS.Python.AuthCode.Sdk)

### Prerequisites
Before you can utilize UPS OAuth APIs SDK, you must obtain the following: 
- A UPS developer account. [Get one now!](https://developer.ups.com/)
- A valid Client ID and Client Secret credentials for your application.


***

# OAuth Using Client Credentials
To get an access token using the Client Credentials Flow, follow these steps:

### Installation
1.Download the files for the Client Credentials SDK [here](https://github.com/UPS-API/UPS-SDKs/tree/Python/UPS.Python.ClientCredentials.Sdk).

2.Install the SDK in your Python Environment `pip install path/to/wheel_file`

3.Use the SDK’s built-in `GetAccessToken` method to send a POST request to the _/oauth/token_ endpoint, and then use the access token in your API requests.


### ClientCredentialService Class

#### About
This class serves as a container for obtaining an access token using the Client Credentials Flow

### Definition
 ```Python
class ClientCredentialService
```


### Constructors
| Definition | Description |
|------------|-------------|
| ClientCredentialService() | Initializes a new instance of the ClientCredentialService class. |

### Methods
| Definition | Description |
|------------|-------------|
| GetAccessToken(clientId, clientSecret, headers, customClaims) | Returns an access token using the provided Client Id, Client Secret, and optional additional request headers or custom claims. |

### Example

#### Creating A Token
```Python
# Replace with your actual client Id and client Secret values
clientId = "YOUR_CLIENT_ID"
clientSecret = "YOUR_CLIENT_SECRET"

# Set additional (if any) values in Http Request Header
httpRequestHeaders = { "HEADER_NAME":"HEADER_VALUE" }

# Set additional (if any) values in Http Request Body
httpRequestBody = { "PROPERTY_NAME", "PROPERTY_VALUE" }

clientCredentialService = ClientCredentialService()

response = await clientCredentialService.GetAccessToken(clientId, clientSecret, httpRequestHeaders, httpRequestBody)
```


## Response Specifications

### Overview
Consuming an SDK will yield a `UpsOauthResponse`. This response object can contain a `TokenInfo` object or an `ErrorResponse` object.

### UpsOauthResponse Class

#### About
This class serves as a container for access token information.

#### Definition
```Python
class UpsOauthResponse
```

#### Properties
| Definition | Description |
|------------|-------------|
| response | A `TokenInfo` object containing information about the token. |
| error | An `ErrorResponse` object containing a list of `ErrorModel` from an unsuccessful API call. |

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```Python
class TokenInfo:
```

#### Properties
| Definition | Description |
|------------|-------------|
| issued_at | Issue time of requested token in milliseconds. |
| token_type | Type of requested access token. |
| client_id | Client id for requested token. |
| access_token | Token to be used in API requests. |
| expires_in | Expire time for requested token in seconds. |
| status | Status for requested token. |

### ErrorResponse Class

#### About
This class contains a list of `ErrorModel` that occurred when attempting to create an access token.

#### Definition
```Python
class ErrorResponse
```

#### Properties
| Definition | Description |
|------------|-------------|
| errors | A list of errors. |

### ErrorModel Class

#### About
This class contains an error code and message based on the response during access token creation.

#### Definition
```Python
class ErrorModel
```

#### Properties
| Definition | Description |
|------------|-------------|
| code | A code representation of the error that occurred. |
| message | A message describing the error that occurred. |

***

# OAuth Using Authorization Code flow
To get an access token using the Authorization code flow, follow these steps:

### Installation
1.Download the files for the Auth Code SDK [here](ups_oauth_authcode_sdk-1.0-py3-none-any.whl).

2.Install the SDK in your Python Environment `pip install path/to/ups_oauth_client_credential_sdk-1.0-py3-none-any.whl`

3.Use the SDK’s built-in `GetAccessToken` method to send a POST request to the _/oauth/token_ endpoint, and then use the access token in your API requests.

***
## AuthCodeService Class

### Definition
```Python
class AuthCodeService
```
A built-in class that contains information for authenticating user and then redirecting to the client application's _redirect uri_ which includes the authorization code, allowing the client application to get the access token.

### Constructors
| Definition | Description |
|------------|-------------|
| AuthCodeService(aiohttp) | Initializes a new instance of the `AuthCodeService` class. Requires an Http Client |

### Methods
| Definition | Description |
|------------|-------------|
| login(query_params) | Initiates the OAuth login flow by redirecting the user to the UPS authorization page. Returns a `UpsOauthResponse` containing a `LoginInfo` object which contains the Redirect URI. |
| get_access_token(client_id, client_secret, redirect_uri, auth_code) | Returns a `UpsOauthResponse` containing a `TokenInfo` object when successful. Requires a Client ID, Client Secret, Redirect URI, and an Auth Code |
| get_access_token_from_refresh_token(client_id, client_secret, refresh_token) | Returns a `UpsOauthResponse` containing a `TokenInfo` object when successful. Requires a Client ID, Client Secret, and a Refresh Token. |

***

## Example

#### Initialize Service
```Python
# Create AuthCodeService object by passing an Http Client.
service = AuthCodeService(aiohttp)
```

#### Initialize Variables
```Python
# Initialize variables to store Access and Refresh Tokens
accessToken = ""
refreshToken = ""

# Initialize variables for credentials and Redirect URI.
clientID = "YOUR_CLIENT_ID"
redirectUri = "YOUR_REDIRECT_URI"
```

#### Logging In
```Python
# Initialize and add query parameters to request.
queryParams = {}
queryParams["client_id"] = clientID
queryParams["redirect_uri"] = redirectUri
queryParams["response_type"] = "code"

# Log in
result = service.login(queryParams)
```

#### Creating A Token
```Python
# Get Auth Code from request query string and assign variables.
authCode = request.args.get('code')
if authCode:
    responseInfo = service.get_access_token(clientID, clientSecret, redirectUri, authCode)
    accessToken = responseInfo['response']['access_token']
    refreshToken = responseInfo['response']['refresh_token']
```

#### Refreshing A Token
```Python
# Use existing Refresh Token and credentials to get a new token.
responseInfo = service.get_access_token_from_refresh_token(clientID, clientSecret, refreshToken);
accessToken = responseInfo['response']['access_token']
refreshToken = responseInfo['response']['refresh_token']
```

***

## Response Specifications

### Overview
Consuming an SDK will yield a `UpsOauthResponse`. This response object can contain a `TokenInfo`, `LoginInfo`, or an `ErrorResponse` object.

### UpsOauthResponse Class

#### About
This class serves as a container for access token information.

#### Definition
```Python
class UpsOauthResponse
```

#### Properties
| Definition | Description |
|------------|-------------|
| response | A `LoginInfo` or `TokenInfo` object containing information about the token. |
| error | An `ErrorResponse` object containing a list of `ErrorModel` from an unsuccessful API call. |

### LoginInfo Class

#### About
This class serves as a container for login information.

#### Definition
```Python
class LoginInfo:
```

#### Properties
| Definition | Description |
|------------|-------------|
| redirect_uri | The redirect URI from the login server. |

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```Python
class TokenInfo:
```

#### Properties
| Definition | Description |
|------------|-------------|
| issued_at | Issue time of requested token in milliseconds. |
| token_type | Type of requested access token. |
| client_id | Client id for requested token. |
| access_token | Token to be used in API requests. |
| expires_in | Expire time for requested token in seconds. |
| status | Status for requested token. |


### ErrorResponse Class

#### About
This class contains a list of `ErrorModel` that occurred when attempting to create an access token.

#### Definition
```Python
class ErrorResponse
```

#### Properties
| Definition | Description |
|------------|-------------|
| errors | A list of errors. |

### ErrorModel Class

#### About
This class contains an error code and message based on the response during access token creation.

#### Definition
```Python
class ErrorModel
```

#### Properties
| Definition | Description |
|------------|-------------|
| code | A code representation of the error that occurred. |
| message | A message describing the error that occurred. |

***

# OAuth Error Codes

#### Error Code List
| Error Code | Description |
|------------|-------------|
| 10400 | Indicates an “Invalid/Missing Authorization Header.” This error is more general and can occur at various stages of the OAuth flow. <br> <br> <u>Possible reasons:</u> <br> - Missing or invalid parameters (e.g., missing response_type, client_id, etc.). <br> - Unauthorized Client. The client (your application) is either not providing a valid authorization code or authorization code is empty. <br> - Unsupported Response Type. The UPS authorization server does not support obtaining an authorization code using the specified method. <br> <br> <u>Solution:</u> <br> Review your request parameters. Make sure your request includes the proper authorization header and ensure compliance with UPS OAuth specifications. <br>|
| 10401 | This error occurs when the “ClientId is invalid.” This error occurs specifically during the client authentication process. It indicates that the client (your application) making the request is not recognized or authorized by the UPS authorization server.<br> <br> <u>Possible reasons:</u> <br> - Incorrect client ID or secret. It could be typographical errors in the client ID or secret. <br> - Unauthorized client (not registered or configured properly). <br> - Mismatched redirect URIs. The URI you provided during authentication maybe doesn’t match any registered redirect URIs.<br> <br>  <u>Solution:</u> <br> Verify your client credentials and ensure proper registration with the UPS authorization server.|

