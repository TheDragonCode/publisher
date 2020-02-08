<?php

namespace Helldar\Publisher\Traits;

use Helldar\Publisher\Contracts\Version;

trait Versionable
{
    /** @var \Helldar\Publisher\Contracts\Version */
    protected $version_concern;

    public function setVersionConcern(string $concern): void
    {
        $this->version_concern = $concern;
    }

    protected function getVersionConcern(string $version = null): Version
    {
        return new $this->version_concern($version);
    }
}
