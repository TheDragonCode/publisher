<?php

namespace Helldar\Publisher\Contracts;

interface Commits
{
    const CONTAINS_ADDED = [
        'add',
        'added',
    ];

    const CONTAINS_CHANGED = [
        'update',
        'updated',
        'change',
        'changed',
        'move',
    ];

    const CONTAINS_FIXED = [
        'fix',
        'fixed',
    ];

    const CONTAINS_REMOVED = [
        'removed',
        'deleted',
        'delete',
    ];

    const EXCLUDE_COMMITERS = [
        'web-flow',
    ];
    const TYPE_ADDED = 'Added';
    const TYPE_CHANGED = 'Changed';
    const TYPE_FIXED = 'Fixed';
    const TYPE_OTHER = 'Other';
    const TYPE_REMOVED = 'Removed';

    public function push(string $hash, string $message = null, string $committer_login = null): void;

    public function count(): int;

    public function grouped(): array;

    public function toText(): ?string;
}
