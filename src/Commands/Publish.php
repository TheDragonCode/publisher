<?php

namespace Helldar\Publisher\Commands;

use Helldar\Publisher\Contracts\Commits;
use Helldar\Publisher\Contracts\Version;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Publish extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('publish')
            ->setDescription('Simple release publication.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        parent::execute($input, $output);

        $this->pushRelease(
            $this->getNewVersion(),
            $this->getCommits()
        );
    }

    protected function getCommits(): Commits
    {
        $this->log->info('Loading commits...');

        return $this->client->commits($this->last_tag);
    }

    protected function getNewVersion(): Version
    {
        $version = clone $this->last_tag;

        $this->askNewVersion($version);
        $this->askIsDraft($version);
        $this->askIsPreRelease($version);

        $text = $this->fullTextVersion($version);

        $accept = $this->getIO()
            ->askConfirmation("Accept " . $text . " version? (yes, y, no or n)" . PHP_EOL);

        if (! $accept) {
            $version = $this->getNewVersion();
        }

        return $version;
    }

    protected function askNewVersion(Version &$version): void
    {
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
    }

    protected function askIsDraft(Version &$version): void
    {
        $accept = $this->getIO()
            ->askConfirmation('Is this a draft? (yes, y, no or n)' . PHP_EOL);

        if ($accept) {
            $version->setDraft();
        }
    }

    protected function askIsPreRelease(Version &$version): void
    {
        $accept = $this->getIO()
            ->askConfirmation('Is this a prerelease? (yes, y, no or n)' . PHP_EOL);

        if ($accept) {
            $version->setPreRelease();
        }
    }

    protected function fullTextVersion(Version $version): string
    {
        $options = [];

        if ($version->isDraft()) {
            $options[] = 'draft';
        }

        if ($version->isPreRelease()) {
            $options[] = 'prerelease';
        }

        return empty($options)
            ? $version->getVersion()
            : \sprintf('%s (%s)', $version->getVersion(), \implode(', ', $options));
    }

    protected function pushRelease(Version $version, Commits $commits): void
    {
        $this->log->info('Publishing a new version ...');
        $this->log->info('Version: ' . $version->getVersion());
        $this->log->info('Commits: ' . $commits->count());
        $this->log->info('');

        $this->log->info(
            $this->client->createTag($version, $commits)
        );
    }
}
