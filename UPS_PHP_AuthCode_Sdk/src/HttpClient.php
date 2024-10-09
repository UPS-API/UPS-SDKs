<?php
namespace UpsPhpAuthCodeSdk;
require_once 'AuthCodeConstants.php';

class HttpClient {
    public function post($url, $headers, $postFields, $requestType) {
        $ch = curl_init();
        if($requestType == 'GET'){
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, AuthCodeConstants::GET_TIMEOUT);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        if($requestType == 'POST'){
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, AuthCodeConstants::POST_TIMEOUT);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirect_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);
        curl_close($ch);
        $response = [
            'status_code' => $httpCode,
            'response' => $response,
            'error_number' => $curl_errno,
            'redirect_url' => $redirect_url
        ];
        return json_encode($response);
    }
}
