<?php

namespace Helldar\Publisher\Entities;

use Helldar\Publisher\Contracts\Version as Versionable;

class Version implements Versionable
{
    /** @var string|null */
    protected $raw;

    /** @var int */
    protected $major = 0;

    /** @var int */
    protected $minor = 0;

    /** @var int */
    protected $patch = 0;

    /** @var string|null */
    protected $manual;

    /** @var bool */
    protected $draft = false;

    /** @var bool */
    protected $prerelease = false;

    public function __construct(string $version = null)
    {
        $this->raw = $version;

        $this->parse();
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

    public function getVersion(): ?string
    {
        return empty($this->manual)
            ? \sprintf('v%s.%s.%s', $this->major, $this->minor, $this->patch)
            : $this->manual;
    }

    public function getVersionRaw(): ?string
    {
        return $this->raw;
    }

    public function setManual(string $version): void
    {
        // TODO: validate pattern

        $this->manual = $version;
    }

    public function noReleases(): bool
    {
        return empty($this->manual) &&
            $this->major === 0 &&
            $this->minor === 0 &&
            $this->patch === 0;
    }

    public function isDraft(): bool
    {
        return $this->draft;
    }

    public function setDraft(bool $is_draft = true): void
    {
        $this->draft = $is_draft;
    }

    public function isPreRelease(): bool
    {
        return $this->prerelease;
    }

    public function setPreRelease(bool $is_prerelease = true): void
    {
        $this->prerelease = $is_prerelease;
    }

    protected function parse(): void
    {
        if (! empty($this->raw)) {
            \preg_match('/(\d*)\.(\d*)(\.*(\d*))/i', $this->raw, $matches);

            $this->major = (int) ($matches[1] ?? 0);
            $this->minor = (int) ($matches[2] ?? 0);
            $this->patch = (int) ($matches[4] ?? 0);
        }
    }
}
