<?php

namespace Helldar\Publisher\Commands;

use Helldar\Publisher\Contracts\Version;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Unpublish extends BaseCommand
{
    /** @var \Helldar\Publisher\Contracts\Versions */
    protected $versions;

    protected function configure()
    {
        $this
            ->setName('unpublish')
            ->setDescription('Simple draft release.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        parent::execute($input, $output);

        $this->loadTags();

        $this->revokeVersion(
            $this->askDraftVersion()
        );
    }

    protected function loadTags(): void
    {
        $this->versions = $this->client->latestTags();
    }

    protected function askDraftVersion(): Version
    {
        $index = $this->getIO()
            ->select('Select version for revoke (default, 0):', $this->getRevokeVersions(), 0);

        /** @var \Helldar\Publisher\Contracts\Version $version */
        $version = $this->versions->getByIndex($index);

        $accept = $this->getIO()
            ->askConfirmation('Are you sure you want to revoke ' . $version->getVersionRaw() . ' version? (yes, y, no or n)' . PHP_EOL);

        return ! $accept
            ? $this->askDraftVersion()
            : $version;
    }

    protected function getRevokeVersions(): array
    {
        $items = [];

        /** @var \Helldar\Publisher\Contracts\Version[] $versions */
        $versions = $this->versions->get();

        foreach ($versions as $key => $version) {
            $items[$key] = $version->getVersionRaw();
        }

        return $items;
    }

    protected function revokeVersion(Version $version)
    {
        $this->log->info('Revoking version ...');
        $this->log->info('Version: ' . $version->getVersionRaw());
        $this->log->info('');

        $this->log->info(
            $this->client->revokeTag($version)
        );
    }
}
