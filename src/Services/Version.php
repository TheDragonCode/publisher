<?php

namespace Helldar\Release\Services;

use Composer\IO\IOInterface;
use Helldar\Release\Services\Github\LatestRelease;
use Symfony\Component\Console\Output\OutputInterface;

use function preg_match;
use function sprintf;

final class Version
{
    public const MAJOR = 1;

    public const MANUAL = 4;

    public const MINOR = 2;

    public const PATCH = 3;

    protected $io;

    protected $output;

    protected $major = 0;

    protected $minor = 0;

    protected $patch = 0;

    public function __construct(IOInterface $io, OutputInterface $output, LatestRelease $latest_release)
    {
        $this->output = $output;
        $this->io     = $io;

        $this->parse($latest_release);
        $this->showCurrent($latest_release);
    }

    public function major(): string
    {
        $this->major++;

        return $this->compile();
    }

    public function minor(): string
    {
        $this->minor++;

        return $this->compile();
    }

    public function patch(): string
    {
        $this->patch++;

        return $this->compile();
    }

    public function manual(): string
    {
        $version = $this->io->ask('Input new package version: ');

        // TODO: validate pattern

        return $version;
    }

    protected function showCurrent(LatestRelease $version)
    {
        $value = $version->noReleases() ? 'not detected' : $version->getVersion();

        $this->output->writeln('Current stable version: ' . $value);
        $this->output->writeln('');
    }

    protected function parse(LatestRelease $version): void
    {
        if ($version->noReleases()) {
            return;
        }

        preg_match('/(\d*)\.(\d*)(\.*(\d*))/i', $version, $matches);

        $this->major = (int) ($matches[1] ?? 0);
        $this->minor = (int) ($matches[2] ?? 0);
        $this->patch = (int) ($matches[4] ?? 0);
    }

    protected function compile(): string
    {
        return sprintf('v%s.%s.%s', $this->major, $this->minor, $this->patch);
    }
}
