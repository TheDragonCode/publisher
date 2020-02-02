<?php

namespace Helldar\Release\Services\Github;

final class NewRelease extends BaseService
{
    protected $version;

    protected $commits;

    public function run(): self
    {
        return $this;
    }

    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    public function setCommits(Commits $commits)
    {
        $this->commits = $commits;
    }
}
