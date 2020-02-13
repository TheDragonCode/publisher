<?php

namespace Helldar\Publisher\Contracts;

interface RemoteFilesystem
{
    public function get(string $relative_url, array $parameters = null);

    public function post(string $relative_url, array $parameters);

    public function setOrigin(string $host): void;

    public function setApiUrl(string $url): void;
}
