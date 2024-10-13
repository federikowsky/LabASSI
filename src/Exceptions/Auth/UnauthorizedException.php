<?php

namespace App\Exceptions\Auth;

use App\Exceptions\BaseException;
use Exception;

class UnauthorizedException extends BaseException
{
    protected $message = 'Access denied due to invalid credentials.';
    protected $code = 403;
    protected $view = 'errors/403';

    public function __construct($message = null, $code = null, Exception $previous = null)
    {
        if ($message !== null) {
            $this->message = $message;
        }
        if ($code !== null) {
            $this->code = $code;
        }
        parent::__construct($this->message,  $this->view, $this->code, $previous);
    }
}