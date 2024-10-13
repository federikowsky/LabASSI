<?php

namespace App\Facades;

use App\Facades\BaseFacade;
use App\Services\Security\EncryptionService;

class Encrypt extends BaseFacade
{
    protected static function get_facade_accessor()
    {
        return EncryptionService::class;
    }
}
