<?php

namespace Helldar\Release\Contracts;

interface CommitTypeable
{
    const ADDED_TYPE = 'Added';
    const CHANGED_TYPE = 'Changed';
    const FIXED_TYPE = 'Fixed';
    const OTHER_TYPE = 'Other';
}
