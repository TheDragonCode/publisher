<?php

namespace Helldar\Publisher\Entities;

use Helldar\Publisher\Contracts\Commit as Commitable;

class Commit implements Commitable
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $hash;

    /** @var string */
    protected $login;

    public function __construct(string $hash, string $message = null, string $committer_login = null)
    {
        $this->message = $message;
        $this->hash    = $hash;
        $this->login   = $committer_login;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getCommitterLogin(): string
    {
        return $this->login;
    }
}
