<?php
namespace UpsPhpAuthCodeSdk;
class TokenInfo {
    public $refresh_token_expires_in;
    public $refresh_token_status;
    public $issued_at;
    public $token_type;
    public $client_id;
    public $access_token;
    public $refresh_token;
    public $refresh_token_issued_at;
    public $expires_in;
    public $status;

    public function to_dict() {
        return array(
            "refresh_token_expires_in" => $this->refresh_token_expires_in,
            "refresh_token_status" => $this->refresh_token_status,
            "issued_at" => $this->issued_at,
            "token_type" => $this->token_type,
            "client_id" => $this->client_id,
            "access_token" => $this->access_token,
            "refresh_token" => $this->refresh_token,
            "refresh_token_issued_at" => $this->refresh_token_issued_at,
            "expires_in" => $this->expires_in,
            "status" => $this->status
        );
    }
}

class LoginInfo {
    public $redirect_uri;
    public function __construct($redirect_uri)
    {
        $this->redirect_uri = $redirect_uri;
    }
    public function to_dict() {
        return array(
            "redirect_uri" => $this->redirect_uri
        );
    }
}
?>