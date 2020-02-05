<?php

namespace Helldar\Release\Commands;

use Composer\Command\BaseCommand;
use Helldar\Release\Contracts\Stubs;
use Helldar\Release\Entities\Commits;
use Helldar\Release\Entities\Version;
use Helldar\Release\Services\Client;
use Helldar\Release\Services\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ReleaseCommand extends BaseCommand implements Stubs
{
    /** @var \Helldar\Release\Services\Client */
    protected $client;

    /** @var \Helldar\Release\Entities\Version */
    protected $release;

    /** @var \Helldar\Release\Services\Log */
    protected $log;

    protected function configure()
    {
        $this
            ->setName('release')
            ->setDescription('Publishes a new version of the release and collects all commits from the previous launch in the description.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setLog($output);
        $this->showPackageName();

        $this->setGithubClient();
        $this->getLatestRelease();

        $this->pushRelease(
            $this->getNewVersion(),
            $this->getCommits()
        );
    }

    protected function getLatestRelease(): void
    {
        $this->log->info('Loading releases...');

        $tag = $this->client->latestTag();

        $this->release = new Version($tag['hash'], $tag['version'], $tag['date']);
    }

    protected function getNewVersion(): Version
    {
        $version = $this->askNewVersion();

        $accept = $this->getIO()
            ->askConfirmation("Accept " . $version->getVersion() . " version? (yes, y, no or n)" . PHP_EOL);

        if (! $accept) {
            $version = $this->getNewVersion();
        }

        return $version;
    }

    protected function askNewVersion(): Version
    {
        $version = new Version(
            $this->release->getHash(),
            $this->release->getVersion(),
            $this->release->getDate()
        );

        $choice = $this->getIO()
            ->select("Select version for increment (default, " . Version::PATCH . "):", [
                Version::MAJOR  => 'major',
                Version::MINOR  => 'minor',
                Version::PATCH  => 'patch',
                Version::MANUAL => 'manual',
            ], Version::PATCH);

        switch ($choice) {
            case Version::MAJOR:
                $version->incrementMajor();
                break;

            case Version::MINOR:
                $version->incrementMinor();
                break;

            case Version::PATCH:
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
        $this->client = new Client($this->getComposer(), $this->getIO(), $this->name());
    }

    protected function getCommits(): Commits
    {
        $this->log->info('Loading commits...');

        return new Commits(
            $this->client->commits(
                $this->release->getDate()
            )
        );
    }

    protected function pushRelease(Version $version, Commits $commits): void
    {
        $this->log->info('Publishing a new version ...');
        $this->log->info('pushed: ' . $version->getVersion());
        $this->log->info('commits count: ' . count($commits->grouped()));

        $content = $this->client->pushTag($version, $commits);

        die(json_encode($content));
    }

    protected function package()
    {
        return $this->getComposer()->getPackage();
    }

    /**
     * @return string|null
     */
    protected function name(): ?string
    {
        // return $this->package()->getName();
        return 'andrey-helldar/testing-ci';
    }

    protected function url(): ?string
    {
        return $this->package()->getSourceUrl();
    }

    protected function showPackageName()
    {
        $this->log->info('Package name: ' . $this->name());
        $this->log->info('');
    }

    protected function setLog(OutputInterface $output)
    {
        $this->log = new Log($this->getIO(), $output);
    }
}
