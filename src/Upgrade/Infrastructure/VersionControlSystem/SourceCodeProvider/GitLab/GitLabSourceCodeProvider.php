<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab;

use Exception;
use Gitlab\Client;
use RuntimeException;
use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class GitLabSourceCodeProvider implements SourceCodeProviderInterface
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider$configurationProvider;

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
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function validateCredentials(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        if (
            !$this->configurationProvider->getAccessToken() ||
            !$this->configurationProvider->getGitLabProjectId()
        ) {
            $stepsExecutionDto->setIsSuccessful(false);
            $stepsExecutionDto->addOutputMessage('Please check defined values of environment variables: ACCESS_TOKEN and GITLAB_PROJECT_ID.');
        }

        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     * @param \Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto $pullRequestDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function createPullRequest(StepsExecutionDto $stepsExecutionDto, PullRequestDto $pullRequestDto): StepsExecutionDto
    {
        try {
            $stepsExecutionDto = $this->validateCredentials($stepsExecutionDto);
            if (!$stepsExecutionDto->getIsSuccessful()) {
                return $stepsExecutionDto;
            }
            $pullRequestId = $this->create($pullRequestDto);
            if ($pullRequestDto->isAutoMerge()) {
                $this->mergePullRequest($pullRequestId);
            }

            return $stepsExecutionDto;
        } catch (Exception $runtimeException) {
            return $stepsExecutionDto
                ->setIsSuccessful(false)
                ->addOutputMessage($runtimeException->getMessage());
        }
    }

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto $pullRequestDto
     *
     * @return int
     *@throws \RuntimeException
     *
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
