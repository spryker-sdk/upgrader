<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab;

use Exception;
use Gitlab\Client;
use RuntimeException;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Dto\SourceCodeProvider\PullRequestDto;
use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class GitLabSourceCodeProvider implements SourceCodeProviderInterface
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected $configurationProvider;

    /**
     * @var \Gitlab\Client
     */
    protected ?Client $gitLabClient = null;

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
        return ConfigurationProvider::GITLAB_SOURCE_CODE_PROVIDER;
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
            !$this->configurationProvider->getGitLabProjectId()
        ) {
            throw new EnvironmentVariableIsNotDefinedException('Please check defined values of environment variables: ACCESS_TOKEN and GITLAB_PROJECT_ID.');
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
            $pullRequestId = $this->create($pullRequestDto);
            if ($pullRequestDto->isAutoMerge()) {
                $this->mergePullRequest($pullRequestId);
            }

            return $stepsExecutionDto;
        } catch (Exception $runtimeException) {
            return $stepsExecutionDto
                ->setIsSuccessful(false)
                ->setOutputMessage($runtimeException->getMessage());
        }
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\SourceCodeProvider\PullRequestDto $pullRequestDto
     *
     * @throws \RuntimeException
     *
     * @return int
     */
    protected function create(PullRequestDto $pullRequestDto): int
    {
        $prCreatingResult = $this->getClient()->mergeRequests()->create(
            $this->configurationProvider->getGitLabProjectId(),
            $pullRequestDto->getSourceBranch(),
            $pullRequestDto->getTargetBranch(),
            $pullRequestDto->getTitle(),
            [
                'description' => $pullRequestDto->getBody(),
            ],
        );
        if (!isset($prCreatingResult['iid'])) {
            throw new RuntimeException('Invalid create PR response.');
        }

        return $prCreatingResult['iid'];
    }

    /**
     * @param int $pullRequestId
     *
     * @return void
     */
    protected function mergePullRequest(int $pullRequestId): void
    {
        sleep($this->configurationProvider->getGitLabDelayBetweenPrCreatingAndMerging());
        $this->getClient()->mergeRequests()->merge(
            $this->configurationProvider->getGitLabProjectId(),
            $pullRequestId,
            [
                'should_remove_source_branch' => true,
                'merge_when_pipeline_succeeds' => true,
            ],
        );
    }

    /**
     * @return \Gitlab\Client
     */
    protected function getClient(): Client
    {
        if (!$this->gitLabClient) {
            $this->gitLabClient = new Client();
            $this->gitLabClient->authenticate($this->configurationProvider->getAccessToken(), Client::AUTH_HTTP_TOKEN);
            if ($this->configurationProvider->getSourceCodeProviderUrl()) {
                $this->gitLabClient->setUrl($this->configurationProvider->getSourceCodeProviderUrl());
            }
        }

        return $this->gitLabClient;
    }
}
