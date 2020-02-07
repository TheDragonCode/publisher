<?php

namespace Helldar\Release\Contracts;

interface Version
{
    public const MAJOR = 1;
    public const MANUAL = 4;
    public const MINOR = 2;
    public const PATCH = 3;

    public function __construct(string $version = null);

    public function incrementMajor(): void;

    public function incrementMinor(): void;

    public function incrementPatch(): void;

    public function setManual(string $version): void;

    public function getVersion(): ?string;

    public function getVersionRaw(): ?string;

    public function noReleases(): bool;
}
