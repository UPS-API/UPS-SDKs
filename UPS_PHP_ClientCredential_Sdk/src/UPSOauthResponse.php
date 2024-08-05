<?php
namespace UpsPhpClientCredentialSdk;

class ErrorModel
{
    private $code;
    private $message;

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }
}

class ErrorResponse
{
    private $errors = [];

    public function __construct($errors = [])
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public function getMessage()
    {
        if ($this->errors && count($this->errors) > 0) {
            return $this->errors[0]->getMessage();
        }
        return null;
    }

    public function addErrorMessage($code, $message)
    {
        $error = new ErrorModel();
        $error->setCode($code);
        $error->setMessage($message);
        $this->errors[] = $error;
    }
    public function to_dict()
    {
        $errorsArray = [];
        foreach ($this->errors as $error) {
            $errorsArray[] = [
                'code' => $error->getCode(),
                'message' => $error->getMessage(),
            ];
        }
        return ['errors' => $errorsArray];
    }
}

class UPSOauthResponse
{
    private $response;
    private $error;

    public function __construct($response = null, $error = null)
    {
        $this->response = $response;
        $this->error = $error;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function to_dict()
    {
        return [
            "response" => $this->response ? $this->response->to_dict() : null,
            "error" => $this->error ? $this->error->to_dict() : null,
        ];
    }
}
?>
