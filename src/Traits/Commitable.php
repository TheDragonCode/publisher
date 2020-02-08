<?php

namespace Helldar\Publisher\Traits;

use Helldar\Publisher\Contracts\Commits;

trait Commitable
{
    /** @var \Helldar\Publisher\Contracts\Commits */
    protected $commits_concern;

    protected function getCommitsConcern(): Commits
    {
        return new $this->commits_concern;
    }

    public function setCommitsConcern(string $concern): void
    {
        $this->commits_concern = $concern;
    }
}
