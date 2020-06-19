<?php

namespace Helldar\Publisher\Services;

use Composer\IO\IOInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

class Log
{
    /** @var \Composer\IO\IOInterface */
    protected $io;

    protected $output;

    public function __construct(IOInterface $io, OutputInterface $output)
    {
        $this->io     = $io;
        $this->output = $output;
    }

    public function error(string $message)
    {
        $this->log(LogLevel::ERROR, $message);
    }

    public function warning(string $message)
    {
        $this->log(LogLevel::WARNING, $message);
    }

    public function notice(string $message)
    {
        $this->log(LogLevel::NOTICE, $message);
    }

    public function info(string $message)
    {
        $this->log(LogLevel::INFO, $message);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed  $level
     * @param  string  $message
     */
    public function log($level, $message)
    {
        if (in_array($level, [LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::ERROR])) {
            $this->output->writeln('<error>' . $message . '</error>', IOInterface::NORMAL);
        } elseif ($level === LogLevel::WARNING) {
            $this->output->writeln('<warning>' . $message . '</warning>', IOInterface::NORMAL);
        } elseif ($level === LogLevel::NOTICE) {
            $this->output->writeln('<info>' . $message . '</info>', IOInterface::VERBOSE);
        } elseif ($level === LogLevel::INFO) {
            $this->output->writeln('<info>' . $message . '</info>', IOInterface::VERY_VERBOSE);
        } else {
            $this->output->writeln($message, IOInterface::DEBUG);
        }
    }
}
