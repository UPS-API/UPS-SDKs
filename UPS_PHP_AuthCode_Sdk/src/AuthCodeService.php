<?php
namespace UpsPhpAuthCodeSdk;
require_once 'AuthCodeConstants.php';
require_once 'TokenInfo.php';
require_once 'UpsOauthResponse.php';
require_once 'HttpClient.php';

class AuthCodeService
{
    private $httpClient;
    public function __construct($httpClient) 
    {  
        $this->httpClient = $httpClient;
    }
    
    public function login($queryParams = null)
    {
        try {
            $url = $this->build_url_with_query_params(AuthCodeConstants::AUTHORIZE_URL, $queryParams);
            $httpHeaders = array(
                "Content-Type: application/x-www-form-urlencoded"
            );
            $response = $this->httpClient->post($url, $httpHeaders, null, 'GET');
            $response=json_decode($response);
            if ($response->status_code == 200 ) {
                $check = $this->login_response($response->redirect_url);
                return json_encode($check);
            } else {
                return json_encode($this->login_error_response($response->response));
            }
        } catch (Exception $e) {
            return $this->api_error_response(AuthCodeConstants::INTERNAL_SERVER_ERROR);
        }
    }
   
    public function get_access_token($client_id, $client_secret, $redirect_uri, $auth_code) {
        try {
            $body = array(
                "grant_type" => "authorization_code",
                "redirect_uri" => $redirect_uri,
                "code" => $auth_code
            );
            $response = $this->post_for_token_info(AuthCodeConstants::TOKEN_URL, $body, $client_id, $client_secret);
            return json_encode($response);
        } catch (Exception $e) {
            return $this->api_error_response(AuthCodeConstants::INTERNAL_SERVER_ERROR);
        }
    }

    public function post_for_token_info($url, $body, $clientId, $clientSecret) {
        try {
            $httpHeaders = array(
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Basic " . base64_encode($clientId . ":" . $clientSecret)
            );
            $response = $this->httpClient->post($url, $httpHeaders, $body, 'POST');
            $response=json_decode($response);
            if ($response->status_code == 200) {
                return $this->api_response($response->response);
            } else if($response->error_number == 0) {
                return $this->api_error_response($response->response);
            } if($response->error_number > 0) {
                return $this->api_error_response(AuthCodeConstants::TIMED_OUT);
            }
        } catch (Exception $e) {
            return $this->api_error_response(AuthCodeConstants::INTERNAL_SERVER_ERROR);
        }
    }

    public function get_access_token_from_refresh_token($client_id, $client_secret, $refresh_token) {
        try {
            $body = array(
                "grant_type" => "refresh_token",
                "refresh_token" => $refresh_token
            );
            $response = $this->post_for_token_info(AuthCodeConstants::REFRESH_TOKEN_URL, $body, $client_id, $client_secret);
            return json_encode($response);
        } catch (Exception $e) {
            return $this->api_error_response(AuthCodeConstants::INTERNAL_SERVER_ERROR);
        }
    }
    public function build_url_with_query_params($base_url, $query_params) {
        if ($query_params == null) {
            return $base_url;
        }
        $query_string = http_build_query($query_params);
        return $base_url . "?" . $query_string;
    }

    public function api_response($response)
    {
        $json = json_decode($response);
        if (isset($json->access_token))
        {
            $token_info = array(
                "access_token" => $json->access_token,
                "client_id" => $json->client_id,
                "expires_in" => $json->expires_in,
                "issued_at" => $json->issued_at,
                "refresh_token" => $json->refresh_token,
                "refresh_token_expires_in" => $json->refresh_token_expires_in,
                "refresh_token_issued_at" => $json->refresh_token_issued_at,
                "refresh_token_status" => $json->refresh_token_status,
                "status" => $json->status,
                "token_type" => $json->token_type
            );
            return (new UpsOauthResponse($token_info, null))->to_dict();
        }
        return api_error_response($response);
    }

    public function api_error_response($json_data) {
        $error_response = json_decode(str_replace("'", '"', $json_data));
        //checking error object format
        if(isset($error_response->response->errors)){
            $errCode=$error_response->response->errors[0]->code;
            $errMsg=$error_response->response->errors[0]->message;
        }else{
            $errCode=$error_response->code;
            $errMsg=$error_response->message;
        }
        $api_error_info = new ErrorModel($errCode,$errMsg);
        return (new UpsOauthResponse(null, $api_error_info))->to_dict();
    }
   
    public function login_response($redirect_uri) {
        $login_info = new LoginInfo($redirect_uri);
        return (new UpsOauthResponse($login_info->to_dict(), null))->to_dict();
    }
   
    public function login_error_response($json_data) {
        $error_response = json_decode($json_data);
        $login_error_info = new ErrorModel(
            $error_response->response->errors[0]->code,
            $error_response->response->errors[0]->message);
        return (new UpsOauthResponse(null, $login_error_info))->to_dict();
    }

}
?>