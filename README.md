# UPS-API SDKs for .Net

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
- Create OAuth Token using Client Credentials. [Get the Nuget Package.](https://github.com/UPS-API/UPS-SDKs/pkgs/nuget/UPS.DotNet.ClientCredentials.SDK)
- Create OAuth Token using an Auth Code and SSO. [Get the Nuget Package.](https://github.com/UPS-API/UPS-SDKs/pkgs/nuget/UPS.DotNet.AuthCode.SDK)

### Prerequisites
Before you can use the SDK to create an access token, ensure you have:
- A UPS developer account. [Get one now!](https://developer.ups.com/)
- A valid Client ID and Client Secret.
- GitHub Packages as a package source for Nuget Package Manager. *This package is **not** available at nuget.org. For more information look [here](https://docs.github.com/en/packages/working-with-a-github-packages-registry/working-with-the-nuget-registry)
***

# OAuth Using Client Credentials

### About
Create an OAuth Token using Client Credentials

### Installation
Add GitHubPackages as a package source from the command line:

`dotnet nuget add source --username USERNAME --password ${{ secrets.GITHUB_TOKEN }} --store-password-in-clear-text --name github "https://nuget.pkg.github.com/ups-api/index.json"`

To install from the command line:

`dotnet add package UPS.DotNet.ClientCredentials.SDK`

### Definition
```C#
public class ClientCredentialService
```

### Constructors
| Definition | Description |
|------------|-------------|
| ClientCredentialService(HttpClient httpClient) | Initializes a new instance of the ClientCredentialService class. |

### Methods
| Definition | Description |
|------------|-------------|
| GetAccessToken(string clientId, string clientSecret) | Returns a token using only the provided id and secret |
| GetAccessToken(string clientId, string clientSecret, Dictionary<string, string> headers) | Returns a token using the provided id, secret, and additional request headers. |
| GetAccessToken(string clientId, string clientSecret, null, Dictionary<string, string> customClaims)| Returns a token using the provided id, secret, and additional request body properties|
| GetAccessToken(string clientId, string clientSecret, Dictionary<string, string> headers, Dictionary<string, string> customClaims) | Returns a token using the provided id, secret, additional request headers, and additional requesty body properties |

### Example

#### Injecting ClientCredentialService
```C#
builder.Services.AddClientCredentialApiClient(httpClient => { });
var app = builder.Build();
```

#### Adding ClientCredentialService to Controller
```C#
public ExampleController(ILogger<ExampleController> logger, IClientCredentialService service, IConfiguration configuration) {
  _logger = logger;
  this.service = service;
  this.configuration = configuration;
}
```

#### Initializing Variables
```C#
// Get ClientID and Secret from secure credential vault.
string clientId  = "YOUR_CLIENT_ID";
string clientSecret  = "YOUR_CLIENT_SECRET";

// Create variable for optional headers
Dictionary<string, string> headers = new Dictionary<string, string>()
{
  { "HEADER_NAME", "HEADER_VALUE" }
};

// Create variable for optional post body
Dictionary<string, string> body = new Dictionary<string, string>()
{
  { "PROPERTY_NAME", "PROPERTY_VALUE" }
};
```

#### Creating A Token
```C#
UpsOauthResponse resp = await this.service.GetAccessToken(clientId, clientSecret, headers, body);

string token = resp?Response?.AccessToken;
```

***

## Response Specifications

### Overview
Consuming an SDK will yield an `UpsOauthResponse`. This response object can contain a `TokenInfo` object or an `ErrorResponse` object.

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```C#
public class TokenInfo
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| IssuedAt | string | Issue time of requested token in milliseconds. |
| TokenType | string | Type of requested access token. |
| ClientId | string | Client id for requested token. |
| AccessToken | string | Token to be used in API requests. |
| ExpiresIn | string | Expire time for requested token in seconds. |
| Status | string | Status for requested token. |

### UpsOauthResponse Class

#### About
This class serves as a container for OAuth authentication response object.

#### Definition
```C#
public class UpsOauthResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| Response | TokenInfo | Provides access to information about the token. |
| Error | ErrorResponse | Contains a list of errors from an unsuccessful API call. |

### ErrorResponse Class

#### About
This class contains a list of errors that occurred when attempting to create an access token.

#### Definition
```C#
public class ErrorResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| Errors | List<ErrorModel> | A list of errors. |

### ErrorModel Class

#### About
This class contains an error code and message based on the response during access token creation.

#### Definition
```C#
public class ErrorModel
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| Code | string | A code representation of the error that occurred. |
| Message | string | A message describing the error that occurred. |


***

# OAuth Using Auth Code

### About
Create an OAuth Token using an Auth Code.

### Installation
To install from the command line:

`dotnet add package UPS.DotNet.AuthCode.SDK`

***

## AuthCodeService Class

### Definition
```C#
public class AuthCodeService
```

### Constructors
| Definition | Description |
|------------|-------------|
| AuthCodeService(HttpClient) | Initializes a new instance of the `AuthCodeService` class. Requiers an `HttpClient` |

### Methods
| Definition | Description |
|------------|-------------|
| Login(HttpContext context) | Initiates the OAuth login flow by redirecting the user to the UPS authorization page. Returns a `Task` containing `LoginInfo` which contains the Redirect URI as a string. |
| Login(HttpContext context, Dictionary<string, string>? queryParams) | Initiates the OAuth login flow by redirecting the user to the UPS authorization page. Returns a `Task` that contains the Redirect URI as a string. Additional Query Parameters may be optionally added. |
| GetAccessToken(string clientId, string clientSecret, string redirectUri, string authCode) | Returns a `Task` containing a `TokenInfo` object when successful. Requires a Client ID, Client Secret, Redirect URI, and an Auth Code |
| GetAccessTokenFromRefreshToken(string clientId, string clientSecret, string refreshToken) | Returns a `Task` containing a `TokenInfo` object. Requires a Client ID, Client Secret, and a Redirect URL. |

***

## Example

#### Injecting AuthCodeService
```C#
//Inject AuthCodeService into your middleware
builder.Services.AddAuthCodeApiClient(httpClient => {});
var app = builder.Build();
```

#### Adding AuthCodeService to Controller
```C#
public ExampleController(ILogger<ExampleController> logger, IAuthCodeService service, IConfiguration configuration) {
  _logger = logger;
  this.service = service;
  this.configuration = configuration;
}
```

#### Initialize Variables
```C#
//Create variables to store Access and Refresh Tokens
string accessToken = string.Empty;
string refreshToken = string.Empty;

// Get ClientID and Secret from secure credential vault.
string clientId  = "YOUR_CLIENT_ID";
string clientSecret  = "YOUR_CLIENT_SECRET";

// Initialize redirect uri
string redirectUri = "YOUR_REDIRECT_URI";
```

#### Logging In
```C#
//Create and add query parameters to request.
Dictionary<string, string> queryParams = new Dictionary<string, string>();
queryParams.Add("client_id", clientID);
queryParams.Add("redirect_uri", redirectUri);
queryParams.Add("response_type", "code");

//Log in
var result = this.service.Login(this.HttpContext, queryParams).Result;
```

#### Creating A Token
```C#
//Get Auth Code from request query string.
string authCode = Convert.ToString(HttpContext.Request.Query["code"]);
if (authCode != "")
{
    var responseInfo = this.service.GetAccessToken(clientID, clientSecret, redirectUri, authCode).Result;
    if (responseInfo != null) {
      this.accessToken = responseInfo?.Response?.AccessToken;
      this.refreshToken = responseInfo?.Response?.RefreshToken;
    }
}
```

#### Refreshing A Token
```C#
//Use existing Refresh Token and credentials to get a new token.
var responseInfo = this.service.GetAccessTokenFromRefreshToken(clientID, clientSecret, refreshToken).Result;
this.accessToken = responseInfo?.Response?.AccessToken;
this.refreshToken = responseInfo?.Response?.RefreshToken;
```

***

## Response Specifications

### Overview
Consuming an SDK will yield an `UpsOauthResponse`. This response object can contain a `TokenInfo` object, an `ErrorResponse` object or a `LoginInfo` object.

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```C#
public class TokenInfo
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| IssuedAt | string | Issue time of requested token in milliseconds. |
| TokenType | string | Type of requested access token. |
| ClientId | string | Client id for requested token. |
| AccessToken | string | Token to be used in API requests. |
| ExpiresIn | string | Expire time for requested token in seconds. |
| Status | string | Status for requested token. |
| RefreshToken | string | Refresh token to be used to refresh existing token. |
| RefreshTokenExpiresIn | String | Expire time for refresh token in seconds. |
| RefreshTokenStatus | String | Status for refresh token. |
| RefreshTokenIssuedAt | String | Time refresh token was issued. |


### UpsOauthResponse Class

#### About
This class serves as a container for OAuth authentication response object.

#### Definition
```C#
public class UpsOauthResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| Response | T | Provides access to information about the token or login information. |
| Error | ErrorResponse | Contains a list of errors from an unsuccessful API call. |

### ErrorResponse Class

#### About
This class contains a list of errors that occurred when attempting to create an access token.

#### Definition
```C#
public class ErrorResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| Errors | List<ErrorModel> | A list of errors. |

### ErrorModel Class

#### About
This class contains an error code and message based on the response during access token creation.

#### Definition
```C#
public class ErrorModel
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| Code | string | A code representation of the error that occurred. |
| Message | string | A message describing the error that occurred. |


***
## LoginInfo Class

### Definition
```C#
public class LoginInfo
```

### Properties

| Definition | Type | Description |
|------------|-------------|------|
| RedirectUri | string | The redirect uri for your application. |

***

# OAuth Error Codes

#### Error Code List
| Error Code | Description |
|------------|-------------|
| 10400 | Indicates an “Invalid/Missing Authorization Header.” This error is more general and can occur at various stages of the OAuth flow. <br> <br> <u>Possible reasons:</u> <br> - Missing or invalid parameters (e.g., missing response_type, client_id, etc.). <br> - Unauthorized Client. The client (your application) is not authorized to request an authorization code. <br> - Unsupported Response Type. The authorization server does not support obtaining an authorization code using the specified method. <br> <br> <u>Solution:</u> <br> Review your request parameters. Make sure your request includes the proper authorization header and ensure compliance with UPS OAuth specifications. <br>|
| 10401 | This error occurs when the “ClientId is invalid.” This error occurs specifically during the client authentication process. It indicates that the client (your application) making the request is not recognized or authorized by the authorization server.<br> <br> <u>Possible reasons:</u> <br> - Incorrect client ID or secret. It could be typographical errors in the client ID or secret. <br> - Unauthorized client (not registered or configured properly). <br> - Mismatched redirect URIs. The URI you provided during authentication maybe doesn’t match any registered redirect URIs.<br> <br>  <u>Solution:</u> <br> Verify your client credentials and ensure proper registration with the authorization server.|
