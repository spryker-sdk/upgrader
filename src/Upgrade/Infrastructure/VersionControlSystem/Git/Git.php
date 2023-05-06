<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Git;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\Generator\PullRequestDataGenerator;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProvider;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class Git
{
    /**
     * @var string
     */
    protected string $commitHash = '';

    /**
     * @var string
     */
    protected string $baseBranch = '';

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface
     */
    protected SourceCodeProviderInterface $sourceCodeProvider;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Generator\PullRequestDataGenerator
     */
    protected PullRequestDataGenerator $pullRequestDataGenerator;

    /**
     * @var array<string>
     */
    protected array $targetFiles = ['*'];

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     * @param \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProvider $sourceCodeProvider
     * @param \Upgrade\Infrastructure\VersionControlSystem\Generator\PullRequestDataGenerator $pullRequestDataGenerator
     */
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

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function isRemoteTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'ls-remote', '--heads', 'origin', $this->getHeadBranch()];
        $process = $this->processRunner->run($command);
        if (strlen($process->getOutput()) > 0) {
            $stepsExecutionDto->setIsSuccessful(false);

            return $stepsExecutionDto;
        }

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
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

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
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

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function createBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'checkout', '-b', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function add(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = array_merge(['git', 'add'], $this->targetFiles);

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function commit(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'commit', '-m', $this->configurationProvider->getCommitMessage()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function push(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'push', '--set-upstream', 'origin', $this->getHeadBranch()];
        $process = $this->processRunner->run($command);

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function validateSourceCodeProviderCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->sourceCodeProvider->validateCredentials($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function createPullRequest(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $composerDiffDto = $stepsExecutionDto->getComposerLockDiff();
        if ($composerDiffDto === null) {
            return $stepsExecutionDto;
        }

        $pullRequestDto = new PullRequestDto(
            $this->getHeadBranch(),
            $this->getBaseBranch(),
            'The result of auto-updating Spryker modules on ' . date('Y-m-d H:i', time()),
            $this->pullRequestDataGenerator->buildBody(
                $composerDiffDto,
                $stepsExecutionDto->getIntegratorResponseDto(),
                $stepsExecutionDto->getBlockerInfo(),
                $stepsExecutionDto->getReportId(),
            ),
            $this->configurationProvider->isPullRequestAutoMergeEnabled(),
        );

        return $this->sourceCodeProvider->createPullRequest($stepsExecutionDto, $pullRequestDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function checkout(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'checkout', $this->getBaseBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function deleteLocalBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'branch', '-D', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function deleteRemoteBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = ['git', 'push', '--delete', 'origin', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function restore(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $restore = array_merge(['git', 'restore'], $this->targetFiles);
        $restoreStaged = array_merge(['git', 'restore', '--staged'], $this->targetFiles);
        $removeUntracked = ['git', 'clean', '-df'];

        $stepsExecutionDto = $this->process($stepsExecutionDto, $restoreStaged);
        $stepsExecutionDto = $this->process($stepsExecutionDto, $restore);

        return $this->process($stepsExecutionDto, $removeUntracked);
    }

    /**
     * @return string
     */
    protected function getHeadBranch(): string
    {
        return sprintf(
            $this->configurationProvider->getBranchPattern(),
            $this->getBaseBranch(),
            $this->getCommitHash(),
        );
    }

    /**
     * @return string
     */
    protected function getBaseBranch(): string
    {
        if ($this->baseBranch === '') {
            $command = ['git', 'rev-parse', '--abbrev-ref', 'HEAD'];
            $process = $this->processRunner->run($command);
            $this->baseBranch = trim($process->getOutput());
        }

        return $this->baseBranch;
    }

    /**
     * @return string
     */
    protected function getCommitHash(): string
    {
        if ($this->commitHash === '') {
            $command = ['git', 'rev-parse', 'HEAD'];
            $process = $this->processRunner->run($command);
            $this->commitHash = trim($process->getOutput());
        }

        return $this->commitHash;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     * @param array<string> $command
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function process(StepsResponseDto $stepsExecutionDto, array $command): StepsResponseDto
    {
        $process = $this->processRunner->run($command);

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     * @param \Symfony\Component\Process\Process<string, string> $process
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    protected function prepareStepsExecutionDto(StepsResponseDto $stepsExecutionDto, Process $process): StepsResponseDto
    {
        $command = str_replace('\'', '', (string)$process->getCommandLine());
        $output = !$process->isSuccessful() ? $process->getErrorOutput() ?: $process->getOutput() : '';
        $outputs = array_filter([$command, $output]);

        $stepsExecutionDto->setIsSuccessful($process->isSuccessful());
        if (!$process->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage(implode(PHP_EOL, $outputs));
        }

        return $stepsExecutionDto;
    }
}
