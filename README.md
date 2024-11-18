# UPS-API SDKs for Java

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
- OAuth Token using **Client Credentials** - This allows the client to directly authorize itself using its client ID and secret. [Get the Package.](https://github.com/UPS-API/UPS-SDKs/pkgs/nuget/UPS.DotNet.AuthCode.SDK)
- OAuth Token using an **Authorization Code flow** - This involves obtaining an authorization code from UPS authorization server, which is then exchanged for an access token. [Get the Package.](https://github.com/UPS-API/UPS-SDKs/pkgs/nuget/UPS.DotNet.ClientCredentials.SDK)

### Prerequisites
Before you can utilize UPS OAuth APIs SDK, you must obtain the following: 
- A UPS developer account. [Get one now!](https://developer.ups.com/)
- A valid client ID and client Secret credentials for your application.
- GitHub Packages as a package source for Nuget Package Manager. *This package is not available at nuget.org. For more information look [here](https://docs.github.com/en/packages/working-with-a-github-packages-registry/working-with-the-apache-maven-registry)

***

# OAuth Using Client Credentials
To get an access token using the Client Credentials Flow, follow these steps:

### Installation
Modify your *~.m2/settings.xml* file:
```
<settings xmlns="http://maven.apache.org/SETTINGS/1.0.0"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://maven.apache.org/SETTINGS/1.0.0
                      http://maven.apache.org/xsd/settings-1.0.0.xsd">

  <activeProfiles>
    <activeProfile>github</activeProfile>
  </activeProfiles>

  <profiles>
    <profile>
      <id>github</id>
      <repositories>
        <repository>
          <id>github</id>
          <url>https://maven.pkg.github.com/ups-api/ups-sdks</url>
          <snapshots>
            <enabled>true</enabled>
          </snapshots>
        </repository>
      </repositories>
    </profile>
  </profiles>

  <servers>
    <server>
      <id>github</id>
      <username>USERNAME</username>
      <password>TOKEN</password>
    </server>
  </servers>
</settings>
```

Add the package to your POM dependencies:
```
<dependency>
  <groupId>ups-sdks</groupId>
  <artifactId>java-sdk-clientcredential</artifactId>
  <version>1.0.0</version>
</dependency>
```

Install the package

`mvn install`

### ClientCredentialService Class

#### About
This class serves as a container for obtaining an access token using the Client Credentials Flow

### Definition
```Java
public class ClientCredentialService
```

### Constructors
| Definition | Description |
|------------|-------------|
| ClientCredentialService(HttpClient) | Initializes a new instance of the ClientCredentialService class. |


### Methods
| Definition | Description |
|------------|-------------|
| getAccessToken(String, String, Dictionary<String, String> , Dictionary<String, String>, Dictionary<String, String>) | Returns an access token using the provided Client Id, Client Secret, and optional headers and request body. |

### Example

#### Creating A Token
```Java
// Get ClientID and Secret from secure credential vault.
String clientId  = "YOUR_CLIENT_ID";
String clientSecret  = "YOUR_CLIENT_SECRET";

Map<String, String> headers = new HashMap<>();
headers.put("YOUR_HEADER", "YOUR_VALUE");

Map<String, String> body = new HashMap<>();
body.put("PROPERTY_NAME", "PROPERTY_VALUE");

// Initialize Service
ClientCredentialService service = new ClientCredentialService();

// Get Access Token
service.getAccessToken(clientId, clientSecret, headers, body);
```

***

## Response Specifications

### Overview
Consuming an SDK will yield an `UpsOauthResponse`. This response object can contain a `TokenInfo` object or an `ErrorResponse` object.

### UpsOauthResponse Class

#### About
This class serves as a container for OAuth authentication response object.

#### Definition
```Java
public class UpsOauthResponse
```

#### Methods
|Definition | Type | Description |
|-----------|------|-------------|
| getResponse() | TokenInfo | Returns a `TokenInfo` object. |
| setResponse(TokenInfo) | void | Sets the response object. Requires a `TokenInfo` object. |
| getError() | ErrorResponse | Returns the `ErrorResponse` object. |
| setError(ErrorResponse) | void | Sets the `ErrorResponse` object. |

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```Java
public class TokenInfo
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
```Java
public class ErrorResponse
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
```Java
public class ErrorModel
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
`mvn install`
***
## AuthCodeService Class

### Definition
```Java
public class AuthCodeService
```
A built-in class that contains information for authenticating user and then redirecting to the client application's _redirect uri_ which includes the authorization code, allowing the client application to get the access token.

### Constructors
| Definition | Description |
|------------|-------------|
| AuthCodeService(HttpClient) | Initializes a new instance of the `AuthCodeService` class. Requires an `HttpClient` |


### Methods
| Definition | Description |
|------------|-------------|
|login(Map<String, String>) | Initiates the OAuth login flow by redirecting the user to the UPS authorization page. Returns a `Map<String, String>` that allows additional Query Parameters to be added. |
|getAccessToken(String, String, String, String, Map<String, String>) | Returns a `APIResponse` containing a `TokenInfo` object when successful. Requires a Client ID, Client Secret, Redirect URI, an Auth Code, and any additional headers required. |
|getAccessTokenFromRefreshToken(String, String, String, Map<String, String>) | Returns an `APIResponse` containing a `TokenInfo` object. Requires a Client ID, Client Secret, a Redirect URL, and any additional headers required. |

### Example

#### Initialize Service
```Java
//Create AuthCodeService object by passing HttpClient.
AuthCodeService service = new AuthCodeService(HttpClient.newHttpClient());
```

#### Initialize Variables
```Java
// Get ClientID and Secret from secure credential vault.
String clientId  = "YOUR_CLIENT_ID";
String clientSecret  = "YOUR_CLIENT_SECRET";

// Assign ClientID and Redirect URI
String clientId  = securevault.getClientId();
String clientSecret  = securevault.getClientSecret();
String redirectUri = "YOUR_REDIRECT_URI";
```

#### Logging In
```Java
//Create and add query parameters to request.
Map<String, String> queryParams = new HashMap<>();
queryParams.put("client_id", clientId);
queryParams.put("redirect_uri", redirectUri);
queryParams.put("response_type", "code");

//Login
CompletableFuture<APIResponse<LoginInfo>> result = service.login(queryParams);
```

#### Creating A Token
```Java
// Get Auth Code from request query string.
String authCode = "";
if (authCode != "") {
  var response = service.getAccessToken(clientId, clientSecret, redirectUri, authCode);
  accessToken = response.Response.getAccessToken();
  refreshToken = response.Response.getRefreshToken();
}   
```

#### Refreshing A Token
```Java

```

***

## Response Specifications

### Overview
Consuming an SDK will yield an `UpsOauthResponse`. This response object can contain a `TokenInfo` object, `LoginInfo` object or an `ErrorResponse` object.

### UpsOauthResponse Class

#### About
This class serves as a container for OAuth authentication response object.

#### Definition
```Java
public class UpsOauthResponse
```

#### Properties
| Definition | Type | Description |
|------------|-------------|------|
| Response | T | Provides access to information about the token. |
| Error | ErrorResponse | Contains a list of errors from an unsuccessful API call. |

#### Methods
|Definition | Type | Description |
|-----------|------|-------------|
| getResponse() | T | Returns the response object. |
| setResponse(T) | void | Sets the response object. |
| getError() | ErrorResponse | Returns the `ErrorResponse` object in the case of failure. |
| setError(ErrorResponse) | void | Sets the `ErrorResponse` object. |

### TokenInfo Class

#### About
This class serves as a container for access token information.

#### Definition
```Java
public class TokenInfo
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
| getRefreshToken() | String | Returns the Refresh Token. |
| getRefreshTokenExpiresIn() | String | Gets the expiration of the Refresh Token. |
| getRefreshTokenIssuedAt() | String | Gets the time the Refresh Token was issued. |
| getRefreshTokenStatus() | String | Gets the status of the Refresh Token. |
| setIssuedAt(String) | void | Sets the issue time of requested token in milliseconds. |
| setTokenType(String) | void | Sets the type of requested access token. |
| setClientId(String) | void | Sets the client id for requested token. |
| setAccessToken() | void | Sets the token to be used in API requests. |
| setExpiresIn() | void | Sets the expire time for requested token in seconds. |
| setStatus() | void | Sets the status for requested token. |
| setRefreshToken(String) | void | Sets the Refresh Token. |
| setRefreshTokenExpiresIn(String) | void | Sets the Refresh Token expiry time. |
| setRefreshTokenIssuedAt(String) | void | Sets the Refresh Token issuance time. |
| setRefreshTokenStatus(String) | void | Sets the Refresh Token status. |

### ErrorResponse Class

#### About
This class contains a list of errors that occurred when attempting to create an access token.

#### Definition
```Java
public class ErrorResponse
```

#### Methods
| Definition | Type | Description |
|------------|------|-------------|
| getErrors() | List<ErrorModel> | Returns a list of errors. |
| setErrors(List<ErrorModel>) | void | Sets a list of errors. |

### ErrorModel Class

#### About
This class contains an error code and message based on the response during access token creation.

#### Definition
```Java
public class ErrorModel
```

#### Methods
| Definition | Type | Description |
|------------|-------------|------|
| getCode() | String | Returns the error code. |
| setCode(String) | void | Sets the error code. |
| getMessage() | String | Returns the error message. |
| setMessage(String) | String | Returns the error message. |

### LoginInfo Class

#### About
This class contains information about the Redirect URI.

#### Definition
```Java
public class LoginInfo
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
