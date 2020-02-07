<?php

namespace Helldar\Release\Traits;

use Helldar\Release\Contracts\Commits;

trait Commitable
{
    /** @var \Helldar\Release\Contracts\Commits */
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
