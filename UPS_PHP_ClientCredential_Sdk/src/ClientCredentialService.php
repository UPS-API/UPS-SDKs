<?php
namespace UpsPhpClientCredentialSdk;

require_once 'ClientCredentialConstants.php';
require_once 'TokenInfo.php';
require_once 'UPSOauthResponse.php';
require_once 'HttpClient.php';

class ClientCredentialService
{
    private $jwtTokenUrlGlobal;
    private $httpClient;

    const DEFAULT_GRANT_TYPE = "client_credentials";
    const DEFAULT_SCOPE = "public";

    public function __construct($httpClient)
    {
        $this->jwtTokenUrlGlobal = ClientCredentialConstants::BASE_URL;
        $this->httpClient = $httpClient;
    }

    public function getAccessToken($clientId, $clientSecret, $headers, $customClaims)
{
    try {
        $url = $this->jwtTokenUrlGlobal;

        $postFields = $this->buildPostFormData($customClaims);
        $authorization = "Basic " . base64_encode("$clientId:$clientSecret");

        $options = [
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: $authorization"
        ];

        $response = $this->httpClient->post($url, array_merge($options, $this->convertHeadersToArray($headers)), $postFields);

        error_log("HTTP response: " . print_r($response, true));

        if ($response['status_code'] == 200) {
            return $this->apiResponse($response['response']);
        } else {
            return $this->apiErrorResponse($response['response']);
        }
    } catch (\Exception $ex) {
        if ($ex instanceof \Exception) {
            return $this->apiErrorResponse(ClientCredentialConstants::TimedOut);
        }
        return $this->apiErrorResponse(ClientCredentialConstants::InternalServerError);
    }
}

    private function buildPostFormData($customClaims)
    {
        $formData = "grant_type=" . self::DEFAULT_GRANT_TYPE . "&scope=" . self::DEFAULT_SCOPE;

        if ($customClaims) {
            foreach ($customClaims as $key => $value) {
                $formData .= "&custom_claims={\"$key\":\"$value\"}";
            }
        }

        return $formData;
    }

    public function apiResponse($response)
    {
        $json = json_decode($response, true);
        if (isset($json['access_token'])) {
            $token_info = new TokenInfo();
            $token_info->setAccessToken($json['access_token']);
            $token_info->setClientId($json['client_id']);
            $token_info->setExpiresIn($json['expires_in']);
            $token_info->setIssuedAt($json['issued_at']);
            $token_info->setStatus($json['status']);
            $token_info->setTokenType($json['token_type']);

            return new UPSOauthResponse($token_info, null);
        }
        return $this->apiErrorResponse($response);
    }


    public function apiErrorResponse($jsonData)
{
    $responseStr = $jsonData;
    $errorResponse = json_decode(str_replace("'", '"', $responseStr), true);

    error_log("Parsed error response: " . print_r($errorResponse, true));

    $errorResponseObj = new ErrorResponse();

    if (is_array($errorResponse)) {
        if (isset($errorResponse['errors'])) {
            foreach ($errorResponse['errors'] as $error) {
                if (isset($error['code']) && isset($error['message'])) {
                    $errorResponseObj->addErrorMessage($error['code'], $error['message']);
                } else {
            
                    error_log("Error entry missing code or message: " . print_r($error, true));
                }
            }
        } else {
            $errorResponseObj->addErrorMessage('Unknown', '' . json_encode($errorResponse));
        }
    } else {
        error_log("Error response is not an array: " . print_r($errorResponse, true));
        $errorResponseObj->addErrorMessage('Unknown', 'Error response is not an array');
    }

    error_log("Populated ErrorResponse: " . print_r($errorResponseObj->to_dict(), true));

    return new UPSOauthResponse(null, $errorResponseObj);
}
    private function convertHeadersToArray($headers)
    {
        $headerArray = [];
        if ($headers) {
            foreach ($headers as $key => $value) {
                $headerArray[] = "$key: $value";
            }
        }
        return $headerArray;
    }
}
?>
