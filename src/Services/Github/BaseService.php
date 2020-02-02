<?php

namespace Helldar\Release\Services\Github;

use Helldar\Release\Contracts\Stubs;
use Helldar\Release\Exceptions\FileNotExistsException;
use Symfony\Thanks\GitHubClient;

use function file_exists;
use function file_get_contents;
use function realpath;
use function str_replace;

abstract class BaseService implements Stubs
{
    /** @var \Symfony\Thanks\GitHubClient */
    protected $client;

    /** @var string */
    protected $owner;

    /** @var string */
    protected $package_name;

    protected $date;

    public function __construct(GitHubClient $client, string $owner, string $package_name)
    {
        $this->client       = $client;
        $this->owner        = $owner;
        $this->package_name = $package_name;
    }

    abstract public function run();

    public function query(string $filename): string
    {
        return str_replace(
            ['{{owner}}', '{{name}}', '{{date}}'],
            [$this->owner, $this->package_name, $this->date],
            $this->getQueryTemplate($filename)
        );
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(string $value = null)
    {
        $this->date = $value ?: '1970-01-01T00:00:00Z';
    }

    protected function getQueryTemplate(string $filename): string
    {
        $path = realpath(__DIR__ . '/../../stubs/' . $filename);

        if (! file_exists($path)) {
            throw new FileNotExistsException($path);
        }

        return file_get_contents($path);
    }
}
