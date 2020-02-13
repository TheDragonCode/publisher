<?php

namespace Helldar\Publisher\Exceptions;

class BadRequestRuntimeException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }
}
