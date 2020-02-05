<?php

namespace Helldar\Release\Entities;

class Commit
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $hash;

    /** @var string */
    protected $date;

    public function __construct(string $message, string $hash, string $date)
    {
        $this->message = $message;
        $this->hash    = $hash;
        $this->date    = $date;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
