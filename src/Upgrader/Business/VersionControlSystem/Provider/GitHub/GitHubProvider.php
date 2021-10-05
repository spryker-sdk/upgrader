<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Provider\GitHub;

use Github\Client;
use Github\HttpClient\Builder;
use Upgrader\Business\VersionControlSystem\Provider\ProviderInterface;
use Upgrader\Business\VersionControlSystem\Response\VcsResponse;
use Upgrader\UpgraderConfig;

class GitHubProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $organization;

    /**
     * @var string
     */
    protected $repository;

    /**
     * @var \Github\Client
     */
    protected $gitClient;

    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    /**
     * @param \Upgrader\UpgraderConfig $config
     */
    public function __construct(UpgraderConfig $config)
    {
        $this->gitClient = $this->getAuthenticatedGitClient($config->getGithubAccessToken());
        $this->config = $config;
        $this->organization = $config->getGithubOrganization();
        $this->repository = $config->getGithubRepository();
    }

    /**
     * Required params:
     * - head: your branch
     * - base: target branch
     * - title (or issue)
     * - body (or issue)
     *
     * @param array $params
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function createPullRequest(array $params): VcsResponse
    {
        $pullRequest = $this->gitClient->pr()->create($this->organization, $this->repository, $params);

        return new VcsResponse(true, sprintf('PR %s has been created', $pullRequest['html_url']));
    }

    /**
     * @param string $token
     *
     * @return \Github\Client
     */
    protected function getAuthenticatedGitClient(string $token): Client
    {
        $gitClient = new Client(new Builder());
        $gitClient->authenticate($token, null, Client::AUTH_ACCESS_TOKEN);

        return $gitClient;
    }
}
