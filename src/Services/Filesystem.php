<?php

namespace Helldar\Publisher\Services;

use Composer\Composer;
use Composer\Downloader\TransportException;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PreFileDownloadEvent;
use Composer\Util\RemoteFilesystem;
use Helldar\Publisher\Contracts\RemoteFilesystem as RemoteFilesystemContract;
use Hirak\Prestissimo\CurlRemoteFilesystem;

class Filesystem implements RemoteFilesystemContract
{
    /** @var \Composer\Composer */
    protected $composer;

    /** @var \Composer\IO\IOInterface */
    protected $io;

    /** @var string */
    protected $origin;

    /** @var string */
    protected $api_url;

    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io       = $io;
    }

    public function get(string $relative_url, array $parameters = null)
    {
        $url = $parameters
            ? $relative_url . '?' . \http_build_query($parameters)
            : $relative_url;

        return $this->call('GET', $url);
    }

    public function post(string $relative_url, array $parameters)
    {
        return $this->call('POST', $relative_url, $parameters);
    }

    public function setOrigin(string $host): void
    {
        $this->origin = $host;
    }

    public function setApiUrl(string $url): void
    {
        $this->api_url = $url;
    }

    protected function call(string $method, string $relative_url, array $parameters = []): string
    {
        $url     = $this->getUrl($relative_url);
        $result  = $this->getContent($method, $url, $parameters);
        $decoded = $this->decodeResult($result);

        return $this->parseContent($decoded);
    }

    protected function getUrl(string $relative_url): string
    {
        $api_url      = \rtrim($this->api_url, '/');
        $relative_url = \ltrim($relative_url, '/');

        return $api_url . '/' . $relative_url;
    }

    protected function getContent(string $method, string $url, array $parameters = [])
    {
        return $this->rfs($url)->getContents($this->origin, $url, false, [
            'http' => [
                'method'  => $method,
                'header'  => ['Accept: application/vnd.github.v3+json'],
                'content' => \json_encode(['query' => $parameters]),
            ],
        ]);
    }

    protected function decodeResult(string $result): array
    {
        return \json_decode($result, true);
    }

    protected function parseContent(array $content)
    {
        if ($content['errors'][0]['message'] ?? false) {
            if ($content['data'] ?? false) {
                throw new TransportException($content['errors'][0]['message']);
            }

            foreach ($content['errors'] as $error) {
                // TODO: change logic
                die(json_encode($error));
            }
        }

        return $content['data'] ?? [];
    }

    protected function rfs(string $url): RemoteFilesystem
    {
        $rfs = Factory::createRemoteFilesystem($this->io, $this->composer->getConfig());

        if ($event_dispatcher = $this->composer->getEventDispatcher()) {
            $pre_file_download_event = $this->preFileDownloadEvent($rfs, $url);

            $event_dispatcher->dispatch($pre_file_download_event->getName(), $pre_file_download_event);

            if (! $pre_file_download_event->getRemoteFilesystem() instanceof CurlRemoteFilesystem) {
                $rfs = $pre_file_download_event->getRemoteFilesystem();
            }
        }

        return $rfs;
    }

    protected function preFileDownloadEvent(RemoteFilesystem $rfs, string $url): PreFileDownloadEvent
    {
        return new PreFileDownloadEvent(PluginEvents::PRE_FILE_DOWNLOAD, $rfs, $url);
    }
}
