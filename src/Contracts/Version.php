<?php

namespace Helldar\Publisher\Contracts;

interface Version
{
    public const MAJOR = 1;

    public const MANUAL = 4;

    public const MINOR = 2;

    public const PATCH = 3;

    public function __construct(string $version = null, int $id = null, bool $is_draft = false, bool $is_prerelease = false);

    public function incrementMajor(): void;

    public function incrementMinor(): void;

    public function incrementPatch(): void;

    public function setManual(string $version): void;

    public function getVersion(): ?string;

    public function getVersionRaw(): ?string;

    public function noReleases(): bool;

    public function isDraft(): bool;

    public function setDraft(bool $is_draft = true): void;

    public function isPreRelease(): bool;

    public function setPreRelease(bool $is_prerelease = true): void;

    public function getId(): ?int;
}
