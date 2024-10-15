<?php

namespace App\Services\OAuth;

abstract class BaseOAuthService
{
    protected $client;

    abstract public function get_auth_url(): string;

    abstract public function authenticate(string $code);

    protected function set_client($client): void
    {
        $this->client = $client;
    }

    public function get_client()
    {
        return $this->client;
    }
}
