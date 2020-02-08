<?php

namespace Helldar\Publisher\Traits;

use Helldar\Publisher\Contracts\Version;
use Helldar\Publisher\Contracts\Versions;

trait Versionable
{
    /** @var \Helldar\Publisher\Contracts\Version */
    protected $version_concern;

    /** @var \Helldar\Publisher\Contracts\Version[] */
    protected $versions_concern;

    protected function getVersionConcern(string $version = null): Version
    {
        return new $this->version_concern($version);
    }

    public function setVersionConcern(string $concern): void
    {
        $this->version_concern = $concern;
    }

    protected function getVersionsConcern(): Versions
    {
        return new $this->versions_concern();
    }

    public function setVersionsConcern(string $concern): void
    {
        $this->versions_concern = $concern;
    }
}
