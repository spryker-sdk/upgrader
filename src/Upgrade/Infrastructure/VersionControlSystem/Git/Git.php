<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Git;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\Generator\PullRequestDataGenerator;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProvider;

class Git
{
    /**
     * @var string
     */
    protected $commitHash = '';

    /**
     * @var string
     */
    protected $baseBranch = '';

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected $configurationProvider;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface
     */
    protected $sourceCodeProvider;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Generator\PullRequestDataGenerator
     */
    protected $pullRequestDataGenerator;

    /**
     * @var array<string>
     */
    protected $targetFiles = ['*'];

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
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function isRemoteTargetBranchNotExist(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
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
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function isLocalTargetBranchNotExist(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
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
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function hasAnyUncommittedChanges(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
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
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function createBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'checkout', '-b', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function add(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = array_merge(['git', 'add'], $this->targetFiles);

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function commit(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'commit', '-m', $this->configurationProvider->getCommitMessage()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function push(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'push', '--set-upstream', 'origin', $this->getHeadBranch()];
        $process = $this->processRunner->run($command);

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function validateSourceCodeProviderCredentials(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->sourceCodeProvider->validateCredentials($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function createPullRequest(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $composerDiffDto = $stepsExecutionDto->getComposerLockDiff();
        if ($composerDiffDto === null) {
            return $stepsExecutionDto;
        }

        $pullRequestDto = new PullRequestDto(
            $this->getHeadBranch(),
            $this->getBaseBranch(),
            'Updated to the latest Spryker modules up to ' . date('m/d/Y h:i', time()),
            $this->pullRequestDataGenerator->buildBody($composerDiffDto, $stepsExecutionDto->getPullRequestWarning()),
            $this->configurationProvider->isPullRequestAutoMergeEnabled(),
        );

        return $this->sourceCodeProvider->createPullRequest($stepsExecutionDto, $pullRequestDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function checkout(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'checkout', $this->getBaseBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function deleteLocalBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'branch', '-D', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function deleteRemoteBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'push', '--delete', 'origin', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function restore(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
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
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     * @param array<string> $command
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function process(StepsExecutionDto $stepsExecutionDto, array $command)
    {
        $process = $this->processRunner->run($command);

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    protected function prepareStepsExecutionDto(StepsExecutionDto $stepsExecutionDto, Process $process): StepsExecutionDto
    {
        $command = str_replace('\'', '', (string)$process->getCommandLine());
        $output = $process->getExitCode() ? $process->getErrorOutput() : '';
        $outputs = array_filter([$command, $output]);

        $stepsExecutionDto->setIsSuccessful($process->isSuccessful());
        if (!$process->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage(implode(PHP_EOL, $outputs));
        }

        return $stepsExecutionDto;
    }
}
