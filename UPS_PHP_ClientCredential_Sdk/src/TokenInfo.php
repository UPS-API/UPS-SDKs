<?php
namespace UpsPhpClientCredentialSdk;

class TokenInfo {
    private $issued_at;
    private $token_type;
    private $client_id;
    private $access_token;
    private $expires_in;
    private $status;

    // Getters and Setters

    public function getIssuedAt() {
        return $this->issued_at;
    }

    public function setIssuedAt($issued_at) {
        $this->issued_at = $issued_at;
    }

    public function getTokenType() {
        return $this->token_type;
    }

    public function setTokenType($token_type) {
        $this->token_type = $token_type;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    public function getAccessToken() {
        return $this->access_token;
    }

    public function setAccessToken($access_token) {
        $this->access_token = $access_token;
    }

    public function getExpiresIn() {
        return $this->expires_in;
    }

    public function setExpiresIn($expires_in) {
        $this->expires_in = $expires_in;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function to_dict() {
        return array(
            "issued_at" => $this->getIssuedAt(),
            "token_type" => $this->getTokenType(),
            "client_id" => $this->getClientId(),
            "access_token" => $this->getAccessToken(),
            "expires_in" => $this->getExpiresIn(),
            "status" => $this->getStatus()
        );
    }
}
?>
