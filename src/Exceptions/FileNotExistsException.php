<?php

namespace Helldar\Release\Exceptions;

use RuntimeException;

class FileNotExistsException extends RuntimeException
{
    public function __construct(string $path)
    {
        $message = "File \"{$path}\" does not exist!";

        parent::__construct($message, 500);
    }
}