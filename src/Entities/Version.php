<?php

namespace Helldar\Release\Entities;

use Helldar\Release\Contracts\Versionable;

class Version implements Versionable
{
    /** @var string|null */
    protected $hash;

    /** @var string|null */
    protected $date;

    /** @var int */
    protected $major = 0;

    /** @var int */
    protected $minor = 0;

    /** @var int */
    protected $patch = 0;

    /** @var string|null */
    protected $manual;

    public function __construct(string $hash = null, string $version = null, string $date = null)
    {
        $this->setHash($hash);
        $this->setVersion($version);
        $this->setDate($date);
    }

    public function incrementMajor(): void
    {
        $this->major++;
        $this->minor = 0;
        $this->patch = 0;
    }

    public function incrementMinor(): void
    {
        $this->minor++;
        $this->patch = 0;
    }

    public function incrementPatch(): void
    {
        $this->patch++;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash = null): void
    {
        $this->hash = $hash;
    }

    public function getVersion(): ?string
    {
        return empty($this->manual)
            ? \sprintf('v%s.%s.%s', $this->major, $this->minor, $this->patch)
            : $this->manual;
    }

    public function setVersion(string $version = null): void
    {
        if (! empty($version)) {
            \preg_match('/(\d*)\.(\d*)(\.*(\d*))/i', $version, $matches);

            $this->major = (int) ($matches[1] ?? 0);
            $this->minor = (int) ($matches[2] ?? 0);
            $this->patch = (int) ($matches[4] ?? 0);
        }
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date = null): void
    {
        $this->date = $date ?: '1970-01-01T00:00:00Z';
    }

    public function setManual(string $version): void
    {
        // TODO: validate pattern

        $this->manual = $version;
    }

    public function noReleases(): bool
    {
        return empty($this->version) && empty($this->manual);
    }
}
