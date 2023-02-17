<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub;

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
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub\GitHubClientFactory
     */
    protected GitHubClientFactory $gitHubClientFactory;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub\GitHubClientFactory $gitHubClientFactory
     */
    public function __construct(ConfigurationProvider $configurationProvider, GitHubClientFactory $gitHubClientFactory)
    {
        $this->configurationProvider = $configurationProvider;
        $this->gitHubClientFactory = $gitHubClientFactory;
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

            $response = $this->gitHubClientFactory->getClient()->pr()->create(
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
}
