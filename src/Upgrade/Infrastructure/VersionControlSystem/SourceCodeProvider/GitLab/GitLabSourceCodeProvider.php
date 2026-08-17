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
use Upgrade\Infrastructure\VersionControlSystem\Generator\OutputMessageBuilder;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class GitLabSourceCodeProvider implements SourceCodeProviderInterface
{
    protected ConfigurationProvider $configurationProvider;

    protected GitLabClientFactory $gitLabClientFactory;

    protected OutputMessageBuilder $outputMessageBuilder;

    public function __construct(
        ConfigurationProvider $configurationProvider,
        GitLabClientFactory $gitLabClientFactory,
        OutputMessageBuilder $outputMessageBuilder
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->gitLabClientFactory = $gitLabClientFactory;
        $this->outputMessageBuilder = $outputMessageBuilder;
    }

    public function getName(): string
    {
        return ConfigurationProvider::GITLAB_SOURCE_CODE_PROVIDER;
    }

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

    public function buildBlockerTextBlock(ValidatorViolationDto $blocker): string
    {
        return sprintf('> <b>%s.</b> %s <br>', $blocker->getTitle(), $blocker->getMessage() . PHP_EOL) . PHP_EOL;
    }

    /**
     * @throws \RuntimeException
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

        $stepsExecutionDto->addOutputMessage(
            $this->outputMessageBuilder->buildOutputMessage($prCreatingResult['web_url'] ?? ''),
        );

        return $prCreatingResult['iid'];
    }

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
