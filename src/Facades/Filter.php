<?php

namespace App\Facades;

use App\Facades\BaseFacade;
use App\Helpers\Filter as FilterHelper;

class Filter extends BaseFacade
{
    protected static function get_facade_accessor()
    {
        return FilterHelper::class;
    }
}
