<?php

namespace App\Facades;

use App\Facades\BaseFacade;
use App\Core\Logger as LoggerHelper;

class Logger extends BaseFacade
{
    protected static function get_facade_accessor()
    {
        return LoggerHelper::class;
    }
}
