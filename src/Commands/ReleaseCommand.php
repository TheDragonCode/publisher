<?php

namespace Helldar\Release\Commands;

use Composer\Command\BaseCommand;
use Helldar\Release\Services\GithubClient;
use Helldar\Release\Services\Version;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ReleaseCommand extends BaseCommand
{
    /** @var \Helldar\Release\Services\GithubClient */
    protected $github;

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    protected $output;

    /** @var \Helldar\Release\Services\Github\LatestRelease */
    protected $latest_release;

    protected function configure()
    {
        $this->setName('z-release')
            ->setDescription('Publishes a new version of the release and collects all commits from the previous launch in the description.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->github = new GithubClient($this->getComposer(), $this->getIO(), $this->name());

        $this->showName();
        $this->loadLatestRelease();

        $this->github->pushRelease(
            $this->latest_release,
            $this->getNewVersion()
        );
    }

    protected function askNewVersion()
    {
        $version = new Version(
            $this->getIO(),
            $this->output,
            $this->latest_release
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
                return $version->major();
            case Version::MINOR:
                return $version->minor();
            case Version::PATCH:
                return $version->patch();
            default:
                return $version->manual();
        }
    }

    protected function getNewVersion(): string
    {
        $version = $this->askNewVersion();

        $accept = $this->getIO()
            ->askConfirmation("Accept " . $version . " version? (yes, y, no or n)" . PHP_EOL);

        if (! $accept) {
            $version = $this->getNewVersion();
        }

        return $version;
    }

    protected function loadLatestRelease()
    {
        $this->latest_release = $this->github->getLatestRelease();
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
//        return $this->package()->getName();
        return 'andrey-helldar/testing-ci';
    }

    protected function url(): ?string
    {
        return $this->package()->getSourceUrl();
    }

    protected function showName()
    {
        $this->output->writeln('Package name: ' . $this->name());
        $this->output->writeln('');
    }
}
