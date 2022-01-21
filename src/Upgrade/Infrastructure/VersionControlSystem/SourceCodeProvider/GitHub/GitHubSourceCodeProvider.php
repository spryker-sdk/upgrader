<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub;

use Github\AuthMethod;
use Github\Client;
use Github\HttpClient\Builder;
use RuntimeException;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Dto\SourceCodeProvider\PullRequestDto;
use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class GitHubSourceCodeProvider implements SourceCodeProviderInterface
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected $configurationProvider;

    /**
     * @var \Github\Client|null
     */
    protected ?Client $gitHubClient = null;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return ConfigurationProvider::GITHUB_SOURCE_CODE_PROVIDER;
    }

    /**
     * @throws \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return void
     */
    public function validateCredentials(): void
    {
        if (
            !$this->configurationProvider->getAccessToken() ||
            !$this->configurationProvider->getOrganizationName() ||
            !$this->configurationProvider->getRepositoryName()
        ) {
            throw new EnvironmentVariableIsNotDefinedException('Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.');
        }
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     * @param \Upgrade\Infrastructure\Dto\SourceCodeProvider\PullRequestDto $pullRequestDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function createPullRequest(StepsExecutionDto $stepsExecutionDto, PullRequestDto $pullRequestDto): StepsExecutionDto
    {
        try {
            $this->validateCredentials();
            $this->getClient()->pr()->create(
                $this->configurationProvider->getOrganizationName(),
                $this->configurationProvider->getRepositoryName(),
                [
                    'base' => $pullRequestDto->getTargetBranch(),
                    'head' => $pullRequestDto->getSourceBranch(),
                    'title' => $pullRequestDto->getTitle(),
                    'body' => $pullRequestDto->getBody(),
                    'auto_merge' => $pullRequestDto->isAutoMerge(),
                ],
            );

            return $stepsExecutionDto;
        } catch (RuntimeException $runtimeException) {
            return $stepsExecutionDto
                ->setIsSuccessful(false)
                ->setOutputMessage($runtimeException->getMessage());
        }
    }

    /**
     * @return \Github\Client
     */
    protected function getClient(): Client
    {
        if (!$this->gitHubClient) {
            $this->gitHubClient = new Client(new Builder());
            $this->gitHubClient->authenticate(
                $this->configurationProvider->getAccessToken(),
                null,
                AuthMethod::ACCESS_TOKEN,
            );
        }

        return $this->gitHubClient;
    }
}
