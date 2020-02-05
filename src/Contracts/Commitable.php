<?php

namespace Helldar\Release\Contracts;

interface Commitable
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
    ];
    const CONTAINS_FIXED = [
        'fix',
        'fixed',
    ];
    const EXCLUDE = [
        'merge pull request',
        '/analysis-',
    ];
    const TYPE_ADDED = 'Added';
    const TYPE_CHANGED = 'Changed';
    const TYPE_FIXED = 'Fixed';
    const TYPE_OTHER = 'Other';
}
