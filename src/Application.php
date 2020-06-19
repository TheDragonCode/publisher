<?php

namespace Helldar\Publisher;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Helldar\Publisher\Commands\CommandProvider;

final class Application implements PluginInterface, Capable
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
