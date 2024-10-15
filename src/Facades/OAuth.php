<?php

namespace App\Facades;

use App\Services\OAuth\OAuthManager;

class OAuth extends BaseFacade
{
    protected static function get_facade_accessor()
    {
        return OAuthManager::class;
    }
}
