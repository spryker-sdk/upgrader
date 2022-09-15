<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub;

use Github\AuthMethod;
use Github\Client;
use Github\HttpClient\Builder;
use RuntimeException;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class GitHubSourceCodeProvider implements SourceCodeProviderInterface
{
    /**
     * @var string
     */
    protected const HTML_URL_KEY = 'html_url';

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Github\Client
     */
    protected Client $gitHubClient;

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
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function validateCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if (
            !$this->configurationProvider->getAccessToken() ||
            !$this->configurationProvider->getOrganizationName() ||
            !$this->configurationProvider->getRepositoryName()
        ) {
            $stepsExecutionDto->setIsSuccessful(false);
            $stepsExecutionDto->addOutputMessage('Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.');
        }

        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     * @param \Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto $pullRequestDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function createPullRequest(StepsResponseDto $stepsExecutionDto, PullRequestDto $pullRequestDto): StepsResponseDto
    {
        try {
            $stepsExecutionDto = $this->validateCredentials($stepsExecutionDto);
            if (!$stepsExecutionDto->getIsSuccessful()) {
                return $stepsExecutionDto;
            }

            $response = $this->gitHubClient->pr()->create(
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

            if (isset($response[static::HTML_URL_KEY])) {
                $stepsExecutionDto->addOutputMessage(
                    sprintf('Pull request was created %s', $response[static::HTML_URL_KEY]),
                );
            }

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
