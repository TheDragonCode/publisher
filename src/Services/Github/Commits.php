<?php

namespace Helldar\Release\Services\Github;

use Helldar\Release\Contracts\CommitTypeable;
use Helldar\Release\Services\Str;

final class Commits extends BaseService implements CommitTypeable
{
    protected $commits = [];

    protected $exclude = [
        'merge pull request',
        '/analysis-',
    ];

    public function run(): self
    {
        $content = $this->client->call(
            $this->query(static::COMMITS_STUB)
        );

        $this->process(
            $content['repository']['defaultBranchRef']['target']['history']['edges']
        );

        return $this;
    }

    protected function process(array $nodes)
    {
        foreach ($nodes as $edge) {
            $node    = $edge['node'];
            $message = $node['messageHeadline'];
            $hash    = $node['oid'];
            $date    = $node['committedDate'];

            if ($this->isAllow($message)) {
                $type = $this->type($message);

                $this->commits[$type][] = \compact('message', 'hash', 'date');
            }
        }
    }

    protected function type(string $message): string
    {
        if ($this->isAdded($message)) {
            return static::ADDED_TYPE;
        }

        if ($this->isFixed($message)) {
            return static::FIXED_TYPE;
        }

        if ($this->isChanged($message)) {
            return static::CHANGED_TYPE;
        }

        return static::OTHER_TYPE;
    }

    protected function isAllow(string $message): bool
    {
        return ! Str::contains(Str::lower($message), $this->exclude);
    }

    protected function isAdded(string $message): bool
    {
        return Str::contains(Str::lower($message), [
            'add',
            'added',
        ]);
    }

    protected function isChanged(string $message): bool
    {
        return Str::contains(Str::lower($message), [
            'update',
            'updated',
            'change',
            'changed',
        ]);
    }

    protected function isFixed(string $message): bool
    {
        return Str::contains(Str::lower($message), [
            'fix',
            'fixed',
        ]);
    }
}
