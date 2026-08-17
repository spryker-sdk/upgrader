<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Git;

use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\Generator\PullRequestDataGenerator;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProvider;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class Git
{
    protected string $baseBranch = '';

    protected ProcessRunnerServiceInterface $processRunner;

    protected ConfigurationProvider $configurationProvider;

    protected SourceCodeProviderInterface $sourceCodeProvider;

    protected PullRequestDataGenerator $pullRequestDataGenerator;

    /**
     * @var array<string>
     */
    protected array $targetFiles = ['*'];

    public function __construct(
        ConfigurationProvider $configurationProvider,
        ProcessRunnerServiceInterface $processRunner,
        SourceCodeProvider $sourceCodeProvider,
        PullRequestDataGenerator $pullRequestDataGenerator
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->processRunner = $processRunner;
        $this->sourceCodeProvider = $sourceCodeProvider->getSourceCodeProvider();
        $this->pullRequestDataGenerator = $pullRequestDataGenerator;
    }

    public function isRemoteTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $targetBranch = $this->getHeadBranch();
        $stepsExecutionDto->setTargetBranch($targetBranch);

        $command = ['git', 'ls-remote', '--heads', 'origin', $targetBranch];
        $process = $this->processRunner->run($command);
        if (strlen($process->getOutput()) > 0) {
            $stepsExecutionDto->setIsSuccessful(false);

            return $stepsExecutionDto;
        }

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    public function isLocalTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'rev-parse', '--verify', $this->getHeadBranch()];
        $process = $this->processRunner->run($command);
        if ($process->isSuccessful()) {
            $stepsExecutionDto->setIsSuccessful(false);
            $stepsExecutionDto->addOutputMessage(implode(PHP_EOL, $command));

            return $stepsExecutionDto;
        }

        $stepsExecutionDto->setIsSuccessful(true);

        return $stepsExecutionDto;
    }

    public function hasAnyUncommittedChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'status', '--porcelain'];
        $process = $this->processRunner->run($command);
        if (strlen($process->getOutput()) > 0) {
            $stepsExecutionDto->setIsSuccessful(false);

            return $stepsExecutionDto;
        }

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    public function createBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'checkout', '-b', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    public function add(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = array_merge(['git', 'add'], $this->targetFiles);

        return $this->process($stepsExecutionDto, $command);
    }

    public function commit(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $commitMessage = $this->configurationProvider->getCommitMessage();

        $releaseGroup = $stepsExecutionDto->getLastAppliedReleaseGroup();
        if ($releaseGroup) {
            $commitMessage = sprintf(
                'Applied release group `%s`, RG link %s',
                $releaseGroup->getName(),
                $releaseGroup->getLink(),
            );
        }

        $command = ['git', 'commit', '-n', '-m', $commitMessage];

        return $this->process($stepsExecutionDto, $command);
    }

    public function push(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'push', '--set-upstream', 'origin', $this->getHeadBranch()];
        $process = $this->processRunner->run($command);

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    public function validateSourceCodeProviderCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->sourceCodeProvider->validateCredentials($stepsExecutionDto);
    }

    public function createPullRequest(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $composerDiffDto = $stepsExecutionDto->getComposerLockDiff();

        if ($composerDiffDto === null && !$stepsExecutionDto->hasErrors()) {
            return $stepsExecutionDto;
        }

        if (!$this->hasCurrentBranchCommits()) {
            $this->createEmptyCommit($stepsExecutionDto, 'pr empty commit');
            $this->push($stepsExecutionDto);
        }

        $releaseGroupId = $this->configurationProvider->getReleaseGroupId();

        $stepsExecutionDto->setIsPullRequestSent(true);

        $pullRequestDto = new PullRequestDto(
            $this->getHeadBranch(),
            $this->getBaseBranch(),
            $this->getPullRequestTitle(
                $releaseGroupId,
                $stepsExecutionDto->getLastAppliedReleaseGroup() ? $stepsExecutionDto->getLastAppliedReleaseGroup()->getJiraIssue() : null,
            ),
            $this->pullRequestDataGenerator->buildBody($stepsExecutionDto, $releaseGroupId),
            $this->configurationProvider->isPullRequestAutoMergeEnabled(),
        );

        return $this->sourceCodeProvider->createPullRequest($stepsExecutionDto, $pullRequestDto);
    }

    protected function getPullRequestTitle(?int $releaseGroupId = null, ?string $jiraIssue = null): string
    {
        $title = 'Auto-updating Spryker modules on ' . date('Y-m-d H:i');

        if ($releaseGroupId !== null) {
            $title .= ' for release group #' . $releaseGroupId;

            if ($jiraIssue !== null) {
                $title .= ' / Jira ticket ' . $jiraIssue;
            }
        }

        return $title;
    }

    public function checkout(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'checkout', $this->getBaseBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    public function deleteLocalBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'branch', '-D', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    public function deleteRemoteBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'push', '--delete', 'origin', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    public function restore(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $restore = array_merge(['git', 'restore'], $this->targetFiles);
        $restoreStaged = array_merge(['git', 'restore', '--staged'], $this->targetFiles);
        $removeUntracked = ['git', 'clean', '-df'];

        $stepsExecutionDto = $this->process($stepsExecutionDto, $restoreStaged);
        $stepsExecutionDto = $this->process($stepsExecutionDto, $restore);

        return $this->process($stepsExecutionDto, $removeUntracked);
    }

    protected function createEmptyCommit(StepsResponseDto $stepsExecutionDto, string $message): StepsResponseDto
    {
        $command = ['git', 'commit', '--allow-empty', '-m', $message];

        return $this->process($stepsExecutionDto, $command);
    }

    protected function getHeadBranch(): string
    {
        $releaseGroupId = $this->configurationProvider->getReleaseGroupId();

        return $releaseGroupId !== null
            ? sprintf($this->configurationProvider->getReleaseGroupBranchPattern(), $this->getBaseBranch(), $releaseGroupId)
            : sprintf($this->configurationProvider->getBranchPattern(), $this->getBaseBranch());
    }

    protected function getBaseBranch(): string
    {
        if ($this->baseBranch === '') {
            $this->baseBranch = $this->configurationProvider->getBuddyRunBranch() ?: $this->getCurrentBranch();
        }

        return $this->baseBranch;
    }

    protected function getCurrentBranch(): string
    {
        $command = ['git', 'rev-parse', '--abbrev-ref', 'HEAD'];
        $process = $this->processRunner->run($command);

        return trim($process->getOutput());
    }

    /**
     * @param array<string> $command
     */
    public function process(StepsResponseDto $stepsExecutionDto, array $command): StepsResponseDto
    {
        $process = $this->processRunner->run($command);

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    /**
     * @param \Symfony\Component\Process\Process<string, string> $process
     */
    protected function prepareStepsExecutionDto(StepsResponseDto $stepsExecutionDto, Process $process): StepsResponseDto
    {
        $command = str_replace('\'', '', (string)$process->getCommandLine());
        $output = !$process->isSuccessful() ? $process->getErrorOutput() ?: $process->getOutput() : '';
        $outputs = array_filter([$command, $output]);

        $stepsExecutionDto->setIsSuccessful($process->isSuccessful());
        if (!$process->isSuccessful()) {
            $stepsExecutionDto->setError(
                Error::createInternalError(implode(PHP_EOL, $outputs)),
            );
        }

        return $stepsExecutionDto;
    }

    protected function hasCurrentBranchCommits(): bool
    {
        $command = ['git', 'log', '--oneline', $this->getBaseBranch() . '..'];

        $process = $this->processRunner->run($command);

        return trim($process->getOutput()) !== '';
    }
}
