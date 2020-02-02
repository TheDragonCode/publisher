<?php

namespace Helldar\Release\Services;

use Composer\Composer;
use Composer\IO\IOInterface;
use Helldar\Release\Services\Github\Commits;
use Helldar\Release\Services\Github\LatestRelease;
use Helldar\Release\Services\Github\NewRelease;
use Symfony\Thanks\GitHubClient as Client;

final class GithubClient
{
    /** @var \Composer\Composer */
    protected $composer;

    /** @var \Composer\IO\IOInterface */
    protected $io;

    /** @var \Symfony\Thanks\GitHubClient */
    protected $client;

    /** @var string */
    protected $owner;

    /** @var string */
    protected $name;

    public function __construct(Composer $composer, IOInterface $io, string $package_name)
    {
        $this->composer = $composer;
        $this->io       = $io;

        $this->client = new Client($composer, $io);

        $this->parseOwnerName($package_name);
    }

    public function getLatestRelease(): LatestRelease
    {
        $service = new LatestRelease($this->client, $this->owner, $this->name);

        return $service->run();
    }

    public function pushRelease(LatestRelease $latest_release, string $new_version)
    {
        $release = $this->release($latest_release, $new_version, $this->commits());
    }

    public function setOwner(string $owner)
    {
        $this->owner = $owner;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function parseOwnerName(string $value)
    {
        [$owner, $name] = \explode('/', $value);

        $this->setOwner($owner);
        $this->setName($name);
    }

    protected function commits(): Commits
    {
        $service = new Commits($this->client, $this->owner, $this->name);

        return $service->run();
    }

    protected function release(LatestRelease $latest_release, string $new_version, Commits $commits): NewRelease
    {
        $service = new NewRelease($this->client, $this->owner, $this->name);
        $service->setDate($latest_release->getDate());
        $service->setVersion($new_version);
        $service->setCommits($commits);

        return $service->run();
    }
}
