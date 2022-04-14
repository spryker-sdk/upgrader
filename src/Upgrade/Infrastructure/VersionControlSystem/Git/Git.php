<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Git;

use Symfony\Component\Process\Process;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException;
use Upgrade\Infrastructure\Process\ProcessRunner;
use Upgrade\Infrastructure\VersionControlSystem\Builder\PullRequestDataBuilder;
use Upgrade\Infrastructure\VersionControlSystem\Provider\SourceCodeProvider;

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
     * @var \Upgrade\Infrastructure\Process\ProcessRunner
     */
    protected ProcessRunner $processRunner;

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected $configurationProvider;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Provider\ProviderInterface
     */
    protected $sourceCodeProvider;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Builder\PullRequestDataBuilder
     */
    protected $pullRequestDataBuilder;

    /**
     * @var array<string>
     */
    protected $targetFiles = ['*'];

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param \Upgrade\Infrastructure\Process\ProcessRunner $processRunner
     * @param \Upgrade\Infrastructure\VersionControlSystem\Provider\SourceCodeProvider $sourceCodeProvider
     * @param \Upgrade\Infrastructure\VersionControlSystem\Builder\PullRequestDataBuilder $pullRequestDataBuilder
     */
    public function __construct(
        ConfigurationProvider $configurationProvider,
        ProcessRunner $processRunner,
        SourceCodeProvider $sourceCodeProvider,
        PullRequestDataBuilder $pullRequestDataBuilder
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->processRunner = $processRunner;
        $this->sourceCodeProvider = $sourceCodeProvider->getSourceCodeProvider();
        $this->pullRequestDataBuilder = $pullRequestDataBuilder;
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function isRemoteTargetBranchNotExist(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'ls-remote', '--heads', $this->getRemote(), $this->getHeadBranch()];
        $process = $this->processRunner->run($command);
        if (strlen($process->getOutput()) > 0) {
            $stepsExecutionDto->setIsSuccessful(false);

            return $stepsExecutionDto;
        }

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function isLocalTargetBranchNotExist(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'rev-parse', '--verify', $this->getHeadBranch()];
        $process = $this->processRunner->run($command);
        if ($process->isSuccessful()) {
            $stepsExecutionDto->setIsSuccessful(false);

            return $stepsExecutionDto;
        }

        $stepsExecutionDto->setIsSuccessful(true);
        $stepsExecutionDto->setOutputMessage(implode(PHP_EOL, $command));

        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
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
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function createBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'checkout', '-b', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function add(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = array_merge(['git', 'add'], $this->targetFiles);

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function commit(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'commit', '-m', $this->configurationProvider->getCommitMessage()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function push(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'push', '--set-upstream', $this->getRemote(), $this->getHeadBranch()];
        $process = $this->processRunner->run($command);

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function createPullRequest(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $composerDiffDto = $stepsExecutionDto->getComposerLockDiff();
        if ($composerDiffDto === null) {
            return $stepsExecutionDto;
        }

        $params = [
            'base' => $this->getBaseBranch(),
            'head' => $this->getHeadBranch(),
            'title' => 'Updated to the latest Spryker modules up to ' . date('m/d/Y h:i', time()),
            'body' => $this->pullRequestDataBuilder->buildBody($composerDiffDto),
            'auto_merge' => $this->configurationProvider->isPullRequestAutoMergeEnabled(),
        ];

        return $this->sourceCodeProvider->createPullRequest($stepsExecutionDto, $params);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function checkout(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'checkout', $this->getBaseBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function deleteLocalBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'branch', '-D', $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function deleteRemoteBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = ['git', 'push', '--delete', $this->getRemote(), $this->getHeadBranch()];

        return $this->process($stepsExecutionDto, $command);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function restore(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $restore = array_merge(['git', 'restore'], $this->targetFiles);
        $restoreStaged = array_merge(['git', 'restore', '--staged'], $this->targetFiles);

        $stepsExecutionDto = $this->process($stepsExecutionDto, $restoreStaged);

        return $this->process($stepsExecutionDto, $restore);
    }

    /**
     * @return string
     */
    protected function getPublicRemote(): string
    {
        $remote = $this->getRemote();

        return substr_replace($remote, str_repeat('*', 10), 8, strpos($remote, '@') - 8);
    }

    /**
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     * @throws \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    protected function getRemote(): string
    {
        if (
            !$this->configurationProvider->getAccessToken() ||
            !$this->configurationProvider->getOrganizationName() ||
            !$this->configurationProvider->getRepositoryName()
        ) {
            throw new EnvironmentVariableIsNotDefinedException('Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.');
        }

        return sprintf(
            'https://%s@github.com/%s/%s.git',
            $this->configurationProvider->getAccessToken(),
            $this->configurationProvider->getOrganizationName(),
            $this->configurationProvider->getRepositoryName(),
        );
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
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     * @param array $command
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function process(StepsExecutionDto $stepsExecutionDto, array $command)
    {
        $process = $this->processRunner->run($command);

        return $this->prepareStepsExecutionDto($stepsExecutionDto, $process);
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    protected function prepareStepsExecutionDto(StepsExecutionDto $stepsExecutionDto, Process $process): StepsExecutionDto
    {
        $command = str_replace('\'', '', (string)$process->getCommandLine());
        $output = $process->getExitCode() ? $process->getErrorOutput() : '';
        $outputs = array_filter([$command, $output]);

        return $stepsExecutionDto
            ->setIsSuccessful($process->isSuccessful())
            ->setOutputMessage(implode(PHP_EOL, $outputs));
    }
}
