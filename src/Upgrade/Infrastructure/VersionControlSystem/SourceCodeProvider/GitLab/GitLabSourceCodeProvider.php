<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab;

use Exception;
use Gitlab\Client;
use RuntimeException;
use Upgrade\Application\Dto\StepsResponseDto;
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
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab\GitLabClientFactory
     */
    protected GitLabClientFactory $gitLabClientFactory;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab\GitLabClientFactory $gitLabClientFactory
     */
    public function __construct(ConfigurationProvider $configurationProvider, GitLabClientFactory $gitLabClientFactory)
    {
        $this->configurationProvider = $configurationProvider;
        $this->gitLabClientFactory = $gitLabClientFactory;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return ConfigurationProvider::GITLAB_SOURCE_CODE_PROVIDER;
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
            !$this->configurationProvider->getGitLabProjectId()
        ) {
            $stepsExecutionDto->setIsSuccessful(false);
            $stepsExecutionDto->addOutputMessage('Please check defined values of environment variables: ACCESS_TOKEN and GITLAB_PROJECT_ID.');
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
            $pullRequestId = $this->create($pullRequestDto);
            if ($pullRequestDto->isAutoMerge()) {
                $this->mergePullRequest($pullRequestId);
            }
            $stepsExecutionDto->addOutputMessage('PR successfully created');

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
     * @throws \RuntimeException
     *
     * @return int
     */
    protected function create(PullRequestDto $pullRequestDto): int
    {
        $prCreatingResult = $this->gitLabClientFactory->getClient()->mergeRequests()->create(
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
        $this->gitLabClientFactory->getClient()->mergeRequests()->merge(
            $this->configurationProvider->getGitLabProjectId(),
            $pullRequestId,
            [
                'should_remove_source_branch' => true,
                'merge_when_pipeline_succeeds' => true,
            ],
        );
    }
}
