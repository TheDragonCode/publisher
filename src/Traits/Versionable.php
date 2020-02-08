<?php

namespace Helldar\Publisher\Traits;

use Helldar\Publisher\Contracts\Version;

trait Versionable
{
    /** @var \Helldar\Publisher\Contracts\Version */
    protected $version;

    public function setVersionConcern(string $concern): void
    {
        $this->version = $concern;
    }

    protected function getVersionConcern(string $version = null): Version
    {
        return new $this->version($version);
    }
}
