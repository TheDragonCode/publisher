<?php

namespace Helldar\Publisher\Commands;

use Composer\Command\BaseCommand as ComposerBaseCommand;
use Composer\Package\RootPackageInterface;
use Helldar\Publisher\Contracts\RemoteFilesystem;
use Helldar\Publisher\Contracts\Version as VersionContract;
use Helldar\Publisher\Entities\Commits;
use Helldar\Publisher\Entities\Version;
use Helldar\Publisher\Entities\Versions;
use Helldar\Publisher\Services\Client;
use Helldar\Publisher\Services\Filesystem;
use Helldar\Publisher\Services\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends ComposerBaseCommand
{
    /** @var \Helldar\Publisher\Services\Client */
    protected $client;

    /** @var VersionContract */
    protected $last_tag;

    /** @var \Helldar\Publisher\Services\Log */
    protected $log;

    /**
     * Package owner.
     *
     * @var string
     */
    protected $owner;

    /**
     * Package name.
     *
     * @var string
     */
    protected $name;

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->autoload();

        $this->setLog($output);
        $this->setPackageOwnerName();

        $this->setGithubClient();
        $this->loadLastTag();
    }

    protected function setLog(OutputInterface $output): void
    {
        $this->log = new Log($this->getIO(), $output);
    }

    protected function setGithubClient(): void
    {
        $this->client = new Client($this->getRemoteFilesystem(), $this->owner, $this->name);

        $this->client->setVersionConcern(Version::class);
        $this->client->setVersionsConcern(Versions::class);
        $this->client->setCommitsConcern(Commits::class);
    }

    protected function getRemoteFilesystem(): RemoteFilesystem
    {
        return new Filesystem($this->getComposer(), $this->getIO());
    }

    protected function loadLastTag(): void
    {
        $this->log->info('Loading releases...');

        $this->last_tag = $this->client->lastTag();

        if ($this->last_tag->noReleases()) {
            $this->log->info('No releases found');
        }
    }

    protected function package(): RootPackageInterface
    {
        return $this->getComposer()->getPackage();
    }

    protected function packageName(): ?string
    {
        return $this->package()->getName();
    }

    protected function url(): ?string
    {
        return $this->package()->getSourceUrl();
    }

    protected function setPackageOwnerName(): void
    {
        if (! ($package = $this->parseSourceUrl())) {
            $package = $this->parsePackageName();
        }

        $this->owner = $package['owner'] ?? null;
        $this->name  = $package['name'] ?? null;

        $this->log->info('Package name: ' . implode('/', $package));
        $this->log->info('');
    }

    protected function parsePackageName(): array
    {
        $package = $this->packageName();

        [$owner, $name] = explode('/', $package);

        return compact('owner', 'name');
    }

    protected function parseSourceUrl(): ?array
    {
        preg_match('/\w*:\/\/github\.com\/(\w+)\/(\w+)\/*(\.git)*/i', $this->url(), $matches);

        if (empty($matches)) {
            return null;
        }

        $owner = $matches[1] ?? null;
        $name  = $matches[2] ?? null;

        return compact('owner', 'name');
    }

    protected function autoload(): void
    {
        $vendor_dir = $this->getComposer()->getConfig()->get('vendor-dir');

        require_once $vendor_dir . '/autoload.php';
    }
}
