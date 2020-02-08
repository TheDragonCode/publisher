<?php

namespace Helldar\Publisher\Entities;

use Helldar\Publisher\Contracts\Commit as Commitable;

class Commit implements Commitable
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $hash;

    public function __construct(string $hash, string $message = null)
    {
        $this->message = $message;
        $this->hash    = $hash;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
