<?php

namespace Helldar\Publisher\Entities;

use Helldar\Publisher\Contracts\Commits as Commitable;
use Helldar\Publisher\Services\Str;

class Commits implements Commitable
{
    /** @var \Helldar\Publisher\Contracts\Commit[] */
    protected $commits = [];

    public function push(string $hash, string $message = null): void
    {
        $this->commits[] = new Commit($hash, $message);
    }

    public function count(): int
    {
        return \count($this->commits);
    }

    protected function contains(string $message, array $values): bool
    {
        return Str::contains(Str::lower($message), $values);
    }

    protected function type(string $message): string
    {
        if ($this->contains($message, static::CONTAINS_ADDED)) {
            return static::TYPE_ADDED;
        }

        if ($this->contains($message, static::CONTAINS_FIXED)) {
            return static::TYPE_FIXED;
        }

        if ($this->contains($message, static::CONTAINS_CHANGED)) {
            return static::TYPE_CHANGED;
        }

        return static::TYPE_OTHER;
    }
}
