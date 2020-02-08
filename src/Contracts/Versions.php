<?php

namespace Helldar\Publisher\Contracts;

interface Versions
{
    public function push(string $version = null, int $id = null, bool $is_draft = false, bool $is_prerelease = false): void;

    public function count(): int;

    public function get(): array;

    public function getByIndex(int $index = null): ?Version;
}
