<?php

namespace Helldar\Publisher\Services;

use Github\Client as GithubClient;
use Helldar\Publisher\Contracts\Commits as CommitsContract;
use Helldar\Publisher\Contracts\Version as VersionContract;
use Helldar\Publisher\Traits\Commitable;
use Helldar\Publisher\Traits\Versionable;
use Http\Adapter\Guzzle6\Client as GuzzleClient;

class Client
{
    use Versionable;
    use Commitable;

    /** @var \Github\Client */
    protected $client;

    /** @var string|null Package name */
    protected $owner;

    /** @var string|null Package owner */
    protected $name;

    /** @var string|null */
    protected $date;

    public function __construct(string $package_owner = null, string $package_name = null)
    {
        $this->owner = $package_owner;
        $this->name  = $package_name;

        $this->configure();
    }

    public function lastTag(): VersionContract
    {
        try {
            $tag = $this->client->repository()->releases()->latest($this->owner, $this->name);

            return $this->getVersionConcern(
                $tag['tag_name'] ?? null
            );
        }
        catch (\Exception $exception) {
            return $this->getVersionConcern();
        }
    }

    public function commits(VersionContract $version): CommitsContract
    {
        try {
            $commits = $version->noReleases()
                ? $this->getAllCommits()
                : $this->getCompareCommits($version->getVersionRaw())['commits'];

            $concern = $this->getCommitsConcern();

            foreach ($commits as $commit) {
                $concern->push(
                    $commit['sha'] ?? null,
                    $commit['commit']['message'] ?? null,
                    $commit['committer']['login'] ?? null
                );
            }

            return $concern;
        }
        catch (\Exception $exception) {
            return $this->getCommitsConcern();
        }
    }

    public function createTag(VersionContract $version, CommitsContract $commits): string
    {
        try {
            $this->client->repository()
                ->releases()
                ->create($this->owner, $this->name, [
                    'tag_name' => $version->getVersion(),
                    'body'     => $commits->toText(),
                ]);

            return 'Tag created successfully';
        }
        catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    protected function getCompareCommits(string $version): array
    {
        return $this->client->repository()
            ->commits()
            ->compare($this->owner, $this->name, $version, 'master');
    }

    protected function getAllCommits(): array
    {
        $params = ['sha' => 'master'];

        return $this->client->repository()
            ->commits()
            ->all($this->owner, $this->name, $params);
    }

    protected function configure()
    {
        $this->client = GithubClient::createWithHttpClient(
            new GuzzleClient()
        );

        $this->client->authenticate('ea0d76bc64ee8a958bf5429aa8574e3057372884', GithubClient::AUTH_HTTP_TOKEN);
    }
}
