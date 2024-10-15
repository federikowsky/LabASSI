<?php

namespace App\Services\OAuth;

use App\Core\ServiceContainer;

class OAuthManager
{
    protected $providers = [];

    public function register_provider(string $name, BaseOAuthService $service)
    {
        $this->providers[$name] = $service;
    }

    public function get_provider(string $name): BaseOAuthService
    {
        if (!isset($this->providers[$name])) {
            $this->load_provider($name);
        }

        if (!isset($this->providers[$name])) {
            throw new \Exception("OAuth provider '$name' not found or could not be loaded.");
        }

        return $this->providers[$name];
    }

    protected function load_provider(string $name): void
    {
        $provider_class = "\\App\\Services\\OAuth\\" . ucfirst(strtolower($name)) . "OAuthService";
        
        if (class_exists($provider_class)) {
            $this->providers[$name] = ServiceContainer::get_container()->getLazy($provider_class);
        }
    }
}
