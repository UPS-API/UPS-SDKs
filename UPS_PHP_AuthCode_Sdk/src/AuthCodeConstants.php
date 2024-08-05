<?php
namespace UpsPhpAuthCodeSdk;
class AuthCodeConstants {
    const AUTHORIZE_URL = "https://onlinetools.ups.com/security/v1/oauth/authorize";
    const TOKEN_URL = "https://onlinetools.ups.com/security/v1/oauth/token";
    const REFRESH_TOKEN_URL = "https://onlinetools.ups.com/security/v1/oauth/refresh";
    const GET_TIMEOUT = 15;  // Timeout in seconds, adjusted from milliseconds
    const POST_TIMEOUT = 15;  // Timeout in seconds, adjusted from milliseconds
    const INTERNAL_SERVER_ERROR = "{\"code\":\"10500\",\"message\":\"Unable to redirect: Response or RequestUri is null.\"}";
    const UNABLE_TO_REDIRECT = "{\"code\":\"10400\",\"message\":\"Unable to redirect: Response or RequestUri is null.\"}";
    const TIMED_OUT = "{\"code\":\"10500\",\"message\":\"Request Timed out.\"}";
    const BASE_URL = "https://onlinetools.ups.com/security/v1/oauth";
}
?>