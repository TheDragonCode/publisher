<?php

namespace Helldar\Release\Entities;

use Helldar\Release\Contracts\Commitable;
use Helldar\Release\Services\Str;

class Commits implements Commitable
{
    /** @var \Helldar\Release\Entities\Commit[] */
    protected $commits = [];

    protected $edges = [];

    public function __construct(array $edges = [])
    {
        $this->edges = $edges;
    }

    public function grouped(): array
    {
        $this->each();

        return $this->getCommits();
    }

    protected function each()
    {
        foreach ($this->edges as $edge) {
            $node    = $edge['node'];
            $message = $node['messageHeadline'];
            $hash    = $node['oid'];
            $date    = $node['committedDate'];

            if (! $this->contains($message, static::EXCLUDE)) {
                $type = $this->type($message);

                $this->push($type, $message, $hash, $date);
            }
        }
    }

    /**
     * @return array|\Helldar\Release\Entities\Commit[]
     */
    protected function getCommits(): array
    {
        return $this->commits;
    }

    protected function push(string $type, string $message, string $hash, string $date): void
    {
        $this->commits[$type][] = new Commit($message, $hash, $date);
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
