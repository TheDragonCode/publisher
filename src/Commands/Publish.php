<?php

namespace Helldar\Publisher\Commands;

use Composer\Command\BaseCommand;
use Helldar\Publisher\Contracts\Commits as CommitsContract;
use Helldar\Publisher\Contracts\Version as VersionContract;
use Helldar\Publisher\Entities\Commits;
use Helldar\Publisher\Entities\Version;
use Helldar\Publisher\Services\Client;
use Helldar\Publisher\Services\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Publish extends BaseCommand
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

    protected function configure()
    {
        $this
            ->setName('publish')
            ->setDescription('Simple publication and recall of releases.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->autoload();

        $this->setLog($output);
        $this->setPackageOwnerName();

        $this->setGithubClient();
        $this->loadLastTag();

        $this->pushRelease(
            $this->getNewVersion(),
            $this->getCommits()
        );
    }

    protected function loadLastTag(): void
    {
        $this->log->info('Loading releases...');

        $this->last_tag = $this->client->lastTag();
    }

    protected function getCommits(): CommitsContract
    {
        $this->log->info('Loading commits...');

        return $this->client->commits($this->last_tag);
    }

    protected function getNewVersion(): VersionContract
    {
        $version = $this->askNewVersion();

        $accept = $this->getIO()
            ->askConfirmation("Accept " . $version->getVersion() . " version? (yes, y, no or n)" . PHP_EOL);

        if (! $accept) {
            $version = $this->getNewVersion();
        }

        return $version;
    }

    protected function askNewVersion(): VersionContract
    {
        $version = clone $this->last_tag;

        $choice = $this->getIO()
            ->select("Select version for increment (default, " . VersionContract::PATCH . "):", [
                VersionContract::MAJOR  => 'major',
                VersionContract::MINOR  => 'minor',
                VersionContract::PATCH  => 'patch',
                VersionContract::MANUAL => 'manual',
            ], VersionContract::PATCH);

        switch ($choice) {
            case VersionContract::MAJOR:
                $version->incrementMajor();
                break;

            case VersionContract::MINOR:
                $version->incrementMinor();
                break;

            case VersionContract::PATCH:
                $version->incrementPatch();
                break;

            default:
                $version->setManual(
                    $this->getIO()->ask('Input new package version: ')
                );
        }

        return $version;
    }

    protected function setGithubClient(): void
    {
        $this->client = new Client($this->owner, $this->name);

        $this->client->setVersionConcern(Version::class);
        $this->client->setCommitsConcern(Commits::class);
    }

    protected function pushRelease(VersionContract $version, CommitsContract $commits): void
    {
        $this->log->info('Publishing a new version ...');
        $this->log->info('Version: ' . $version->getVersion());
        $this->log->info('Commits: ' . $commits->count());
        $this->log->info('');

        $this->log->info(
            $this->client->createTag($version, $commits)
        );
    }

    protected function package()
    {
        return $this->getComposer()->getPackage();
    }

    /**
     * @return string|null
     */
    protected function packageName(): ?string
    {
        return $this->package()->getName();
    }

    protected function url(): ?string
    {
        return $this->package()->getSourceUrl();
    }

    protected function setPackageOwnerName()
    {
        $package = $this->packageName();

        [$owner, $name] = \explode('/', $package);

        $this->owner = $owner;
        $this->name  = $name;

        $this->log->info('Package name: ' . $package);
        $this->log->info('');
    }

    protected function setLog(OutputInterface $output)
    {
        $this->log = new Log($this->getIO(), $output);
    }

    protected function autoload()
    {
        $vendor_dir = $this->getComposer()->getConfig()->get('vendor-dir');

        require_once $vendor_dir . '/autoload.php';
    }
}
