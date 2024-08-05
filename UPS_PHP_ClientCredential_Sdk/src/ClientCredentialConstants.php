<?php
namespace UpsPhpClientCredentialSdk;
class ClientCredentialConstants {
    const BASE_URL = "https://onlinetools.ups.com/security/v1/oauth/token";
    const TIMED_OUT = '{"response":{"errors":[{"code":"10500","message":"Request Timed out."}]}}';
    const INTERNAL_SERVER_ERROR = '{"response":{"errors":[{"code":"10500","message":"Unable to redirect: Response or RequestUri is null."}]}}';
    const POST_TIMEOUT = 15;
}
?>
