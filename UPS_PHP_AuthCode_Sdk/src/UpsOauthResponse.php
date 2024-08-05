<?php
namespace UpsPhpAuthCodeSdk;
class UpsOauthResponse
{
    public $response;
    public $error;

    public function __construct($response = null, $error = null)
    {
        $this->response = $response;
        $this->error = $error;
    }

    public function to_dict()
    {
        return [
            "response" => $this->response,
            "error" => $this->error,
        ];
    }
}

class ErrorModel
{
    public $code;
    public $message;

    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }
}
?>