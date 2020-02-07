<?php

namespace Helldar\Release\Traits;

use Helldar\Release\Contracts\Version;

trait Versionable
{
    /** @var \Helldar\Release\Contracts\Version */
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
