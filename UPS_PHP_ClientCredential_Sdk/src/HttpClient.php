<?php
namespace UpsPhpClientCredentialSdk;

class HttpClient
{
    private $timeout;
    
    public function __construct($timeout = 15) {
        $this->timeout = $timeout;
    }

    public function post($url, $headers, $postFields)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);  // Set timeout

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error_msg = curl_error($ch);

        curl_close($ch);

        if (!empty($error_msg)) {
            throw new \Exception($error_msg);
        }
        
        return [
            'status_code' => $statusCode,
            'response' => $response
        ];
    }
}
?>
