<?php

namespace Helldar\Release\Commands;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

final class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return [
            new ReleaseCommand(),
        ];
    }
}
