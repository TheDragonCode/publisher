<?php

namespace Helldar\Release\Contracts;

interface Commit
{
    public function __construct(string $hash, string $message = null);

    public function getMessage(): ?string;

    public function getHash(): string;
}
