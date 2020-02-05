<?php

namespace Helldar\Release\Services;

use Composer\Composer;
use Composer\IO\IOInterface;
use Helldar\Release\Contracts\Stubs;
use Helldar\Release\Entities\Commits;
use Helldar\Release\Entities\Version;
use Helldar\Release\Exceptions\FileNotExistsException;
use Symfony\Thanks\GitHubClient;

class Client implements Stubs
{
    /** @var \Composer\Composer */
    protected $composer;

    /** @var \Composer\IO\IOInterface */
    protected $io;

    /** @var string */
    protected $package_name;

    /** @var \Symfony\Thanks\GitHubClient */
    protected $client;

    /** @var string */
    protected $owner;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $date;

    public function __construct(Composer $composer, IOInterface $io, string $package_name)
    {
        $this->composer     = $composer;
        $this->io           = $io;
        $this->package_name = $package_name;
        $this->client       = new GitHubClient($composer, $io);

        $this->parseName($package_name);
    }

    public function call(string $filename)
    {
        return $this->client->call(
            $this->query($filename)
        );
    }

    public function latestTag(): array
    {
        $content = $this->call(static::LAST_TAG_STUB);

        $node    = $content['repository']['tags']['edges'][0]['node'] ?? [];
        $hash    = $node['target']['sha'] ?? null;
        $version = $node['name'] ?? null;
        $date    = $node['target']['author']['date'] ?? null;

        return \compact('hash', 'version', 'date');
    }

    public function commits(string $date): array
    {
        $this->date = $date;

        $content = $this->call(static::COMMITS_STUB);

        return $content['repository']['defaultBranchRef']['target']['history']['edges'];
    }

    public function pushTag(Version $version, Commits $commits)
    {
        return $this->call(static::NEW_TAG_STUB);
    }

    protected function parseName(string $package_name): void
    {
        [$owner, $name] = \explode('/', $package_name);

        $this->owner = $owner;
        $this->name  = $name;
    }

    protected function query(string $filename): string
    {
        return \str_replace(
            ['{{owner}}', '{{name}}', '{{date}}'],
            [$this->owner, $this->name, $this->date],
            $this->queryTemplate($filename)
        );
    }

    protected function queryTemplate(string $filename): string
    {
        $path = \realpath(__DIR__ . '/../../stubs/' . $filename);

        if (! \file_exists($path)) {
            throw new FileNotExistsException($path);
        }

        return \file_get_contents($path);
    }
}
