<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab;

use Exception;
use RuntimeException;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class GitLabSourceCodeProvider implements SourceCodeProviderInterface
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

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
            !$this->configurationProvider->getAccessToken()
            || (!$this->configurationProvider->getProjectId()
                && (!$this->configurationProvider->getOrganizationName() || !$this->configurationProvider->getRepositoryName()))
        ) {
            $stepsExecutionDto->setIsSuccessful(false);

            $stepsExecutionDto->setError(
                Error::createInternalError('Please check defined values of environment variables: ACCESS_TOKEN and (PROJECT_ID or (ORGANIZATION_NAME and REPOSITORY_NAME)).'),
            );
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
            $pullRequestId = $this->create($pullRequestDto, $stepsExecutionDto);
            if ($pullRequestDto->isAutoMerge()) {
                $this->mergePullRequest($pullRequestId);
            }

            return $stepsExecutionDto;
        } catch (Exception $runtimeException) {
            return $stepsExecutionDto
                ->setIsSuccessful(false)
                ->setError(Error::createInternalError($runtimeException->getMessage()));
        }
    }

    /**
     * @param \Upgrade\Application\Dto\ValidatorViolationDto $blocker
     *
     * @return string
     */
    public function buildBlockerTextBlock(ValidatorViolationDto $blocker): string
    {
        return sprintf('> <b>%s.</b> %s <br>', $blocker->getTitle(), $blocker->getMessage() . PHP_EOL) . PHP_EOL;
    }

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto $pullRequestDto
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @throws \RuntimeException
     *
     * @return int
     */
    protected function create(PullRequestDto $pullRequestDto, StepsResponseDto $stepsExecutionDto): int
    {
        $prCreatingResult = $this->gitLabClientFactory->getClient()->mergeRequests()->create(
            $this->getProjectId(),
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

        $stepsExecutionDto->addOutputMessage(sprintf('Pull request was created %s', $prCreatingResult['web_url'] ?? ''));

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
            $this->getProjectId(),
            $pullRequestId,
            [
                'should_remove_source_branch' => true,
                'merge_when_pipeline_succeeds' => true,
            ],
        );
    }

    /**
     * @return string
     */
    protected function getProjectId(): string
    {
        $gitLabProjectId = $this->configurationProvider->getProjectId();

        if ($gitLabProjectId !== '') {
            return $gitLabProjectId;
        }

        return sprintf(
            '%s/%s',
            $this->configurationProvider->getOrganizationName(),
            $this->configurationProvider->getRepositoryName(),
        );
    }
}
