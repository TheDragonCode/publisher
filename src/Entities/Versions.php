<?php

namespace Helldar\Publisher\Entities;

use Helldar\Publisher\Contracts\Version as VersionContract;
use Helldar\Publisher\Contracts\Versions as VersionsContract;

class Versions implements VersionsContract
{
    /** @var \Helldar\Publisher\Contracts\Version[] */
    protected $versions = [];

    public function push(string $version = null, int $id = null, bool $is_draft = false, bool $is_prerelease = false): void
    {
        $this->versions[] = new Version($version, $id, $is_draft, $is_prerelease);
    }

    public function count(): int
    {
        return count($this->versions);
    }

    /**
     * @return \Helldar\Publisher\Contracts\Version[]
     */
    public function get(): array
    {
        return $this->versions;
    }

    public function getByIndex(int $index = null): ?VersionContract
    {
        return $this->versions[$index] ?? null;
    }
}
