<?php

namespace Helldar\Release\Services\Github;

final class LatestRelease extends BaseService
{
    protected $no_releases = false;

    protected $version;

    protected $hash;

    public function run(): self
    {
        $content = $this->client->call(
            $this->query(static::LAST_TAG_STUB)
        );

        if (! isset($content['repository']['tags']['edges'][0]['node'])) {
            $this->no_releases = true;

            return $this;
        }

        $node = $content['repository']['tags']['edges'][0]['node'];

        $this->setVersion($node['name']);
        $this->setHash($node['target']['sha']);
        $this->setDate($node['target']['author']['date']);

        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    protected function setVersion(string $value)
    {
        $this->version = $value;
    }

    public function getHash()
    {
        return $this->hash;
    }

    protected function setHash(string $value)
    {
        $this->hash = $value;
    }

    public function noReleases(): bool
    {
        return $this->no_releases;
    }
}
