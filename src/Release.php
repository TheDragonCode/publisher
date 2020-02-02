<?php

namespace Helldar\Release;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Helldar\Release\Commands\CommandProvider;

final class Release implements PluginInterface, Capable
{
    public function activate(Composer $composer, IOInterface $io)
    {
        //
    }

    /**
     * @return string[]
     */
    public function getCapabilities()
    {
        return [
            CommandProviderCapability::class => CommandProvider::class,
        ];
    }
}
