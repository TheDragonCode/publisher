<?php

namespace Helldar\Publisher\Entities;

use Helldar\Publisher\Contracts\Commits as Commitable;
use Helldar\Publisher\Services\Str;

class Commits implements Commitable
{
    /** @var \Helldar\Publisher\Contracts\Commit[] */
    protected $commits = [];

    /** @var array */
    protected $groups = [];

    public function push(string $hash, string $message = null, string $committer_login = null): void
    {
        $this->commits[] = new Commit($hash, $message, $committer_login);
    }

    public function count(): int
    {
        return count($this->commits);
    }

    public function toText(): ?string
    {
        $text = '';

        foreach ($this->grouped() as $group_name => $values) {
            $text .= sprintf('## %s%s', $group_name, PHP_EOL);

            foreach ($values as $message => $hashes) {
                $text .= sprintf('* %s (%s)%s', $message, implode(', ', $hashes), PHP_EOL);
            }

            $text .= PHP_EOL;
        }

        return $text;
    }

    public function grouped(): array
    {
        if (empty($this->groups)) {
            $this->groups = $this->grouping();
        }

        return $this->groups;
    }

    protected function grouping(): array
    {
        $groups = [];

        foreach ($this->commits as $commit) {
            if (in_array($commit->getCommitterLogin(), static::EXCLUDE_COMMITERS)) {
                continue;
            }

            $message = $commit->getMessage();
            $hash    = $commit->getHash();
            $type    = $this->type($message);

            if (isset($groups[$type][$message])) {
                $groups[$type][$message][] = $hash;
            } else {
                $groups[$type][$message] = [$hash];
            }
        }

        ksort($groups);

        return $groups;
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

        if ($this->contains($message, static::CONTAINS_REMOVED)) {
            return static::TYPE_REMOVED;
        }

        return static::TYPE_OTHER;
    }
}
