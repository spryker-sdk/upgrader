<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Provider\GitHub;

use Github\AuthMethod;
use Github\Client;
use Github\HttpClient\Builder;
use RuntimeException;
use Upgrade\Application\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Provider\ProviderInterface;

class GitHubProvider implements ProviderInterface
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected $configurationProvider;

    /**
     * @var \Github\Client
     */
    protected $gitHubClient;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
        $this->gitHubClient = $this->authenticated($configurationProvider->getAccessToken());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return ConfigurationProvider::GITHUB_SOURCE_CODE_PROVIDER;
    }

    /**
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     * @param array $params
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function createPullRequest(StepsExecutionDto $stepsExecutionDto, array $params): StepsExecutionDto
    {
        try {
            $this->gitHubClient->pr()->create(
                $this->configurationProvider->getOrganizationName(),
                $this->configurationProvider->getRepositoryName(),
                $params,
            );

            return $stepsExecutionDto;
        } catch (RuntimeException $runtimeException) {
            return $stepsExecutionDto
                ->setIsSuccessful(false)
                ->addOutputMessage($runtimeException->getMessage());
        }
    }

    /**
     * @param string $token
     *
     * @return \Github\Client
     */
    protected function authenticated(string $token): Client
    {
        $gitClient = new Client(new Builder());
        $gitClient->authenticate($token, null, AuthMethod::ACCESS_TOKEN);

        return $gitClient;
    }
}
