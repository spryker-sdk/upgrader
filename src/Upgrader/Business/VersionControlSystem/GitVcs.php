<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem;

use Symfony\Component\Process\Process;
use Upgrader\Business\VersionControlSystem\Provider\ProviderInterface;
use Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection;
use Upgrader\Business\VersionControlSystem\Response\VcsResponse;
use Upgrader\UpgraderConfig;

class GitVcs implements VcsInterface
{
    /**
     * @var string|null
     */
    protected $commitHash;

    /**
     * @var string|null
     */
    protected $baseBranch;

    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    /**
     * @var \Upgrader\Business\VersionControlSystem\Provider\ProviderInterface
     */
    protected $provider;

    /**
     * @var array<string>
     */
    protected $targetFiles = ['composer.lock', 'composer.json', 'integrator.lock'];

    /**
     * @param \Upgrader\UpgraderConfig $config
     * @param \Upgrader\Business\VersionControlSystem\Provider\ProviderInterface $provider
     */
    public function __construct(UpgraderConfig $config, ProviderInterface $provider)
    {
        $this->config = $config;
        $this->provider = $provider;
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function addChanges(): VcsResponse
    {
        $command = array_merge(['git', 'add'], $this->targetFiles);
        $process = $this->runProcess($command);

        return $this->createResponseForProcess($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function createBranch(string $branch): VcsResponse
    {
        $command = ['git', 'checkout', '-b', $branch];
        $process = $this->runProcess($command);

        return $this->createResponseForProcess($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function deleteLocalBranch(string $branch): VcsResponse
    {
        $command = ['git', 'branch', '-D', $branch];
        $process = $this->runProcess($command);

        return $this->createResponseForProcess($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function deleteRemoteBranch(string $branch): VcsResponse
    {
        $command = ['git', 'push', '--delete', $this->getRemote(), $branch];
        $process = $this->runProcess($command);
        $command[3] = $this->getPublicRemote();

        return $this->createResponseForProcess($process, implode(' ', $command));
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection
     */
    public function revertUncommittedChanges(): VcsResponseCollection
    {
        $collection = new VcsResponseCollection();
        $collection->add($this->restoreStaged());
        $collection->add($this->restore());

        return $collection;
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    protected function restoreStaged(): VcsResponse
    {
        $command = array_merge(['git', 'restore', '--staged'], $this->targetFiles);
        $process = $this->runProcess($command);

        return $this->createResponseForProcess($process);
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    protected function restore(): VcsResponse
    {
        $command = array_merge(['git', 'restore'], $this->targetFiles);
        $process = $this->runProcess($command);

        return $this->createResponseForProcess($process);
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection
     */
    public function rollback(): VcsResponseCollection
    {
        $collection = new VcsResponseCollection();
        $collection->add($this->deleteLocalBranch($this->getHeadBranch()));
        $collection->add($this->deleteRemoteBranch($this->getHeadBranch()));
        if ($this->hasUncommittedChanges()) {
            $collection->addCollection($this->revertUncommittedChanges());
        }

        return $collection;
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function checkout(): VcsResponse
    {
        $command = ['git', 'checkout', $this->getBaseBranch()];
        $process = $this->runProcess($command);

        return $this->createResponseForProcess($process);
    }

    /**
     * @param string $message
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function commitChanges(string $message): VcsResponse
    {
        $command = ['git', 'commit', '-m', $message];
        $process = $this->runProcess($command);

        return $this->createResponseForProcess($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function pushChanges(string $branch): VcsResponse
    {
        $command = ['git', 'push', '--set-upstream', $this->getRemote(), $branch];
        $process = $this->runProcess($command);
        $command[3] = $this->getPublicRemote();

        return $this->createResponseForProcess($process, implode(' ', $command));
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function checkUncommittedChanges(): VcsResponse
    {
        if ($this->hasUncommittedChanges()) {
            return $this->createResponse(false, 'You have to fix uncommitted changes');
        }

        return $this->createResponse(true, "You don't have uncommitted changes");
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function checkTargetBranchExists(): VcsResponse
    {
        if ($this->targetBranchExists()) {
            return $this->createResponse(false, 'You have an unprocessed PR from a previous update. Upgrader can\'t provide a new update until you process these changes');
        }

        return $this->createResponse(true, "You don't have an unprocessed PR from a previous update");
    }

    /**
     * @return bool
     */
    public function hasUncommittedChanges(): bool
    {
        $command = ['git', 'status', '--porcelain'];
        $process = $this->runProcess($command);

        return strlen($process->getOutput()) > 0;
    }

    /**
     * @return bool
     */
    public function targetBranchExists(): bool
    {
        $command = ['git', 'ls-remote', '--heads', $this->getRemote(), $this->getHeadBranch()];
        $process = $this->runProcess($command);

        return strlen($process->getOutput()) > 0;
    }

    /**
     * @param array<string> $releaseGroups
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function createPullRequest(array $releaseGroups): VcsResponse
    {
        $params = [
            'base' => $this->getBaseBranch(),
            'head' => $this->getHeadBranch(),
            'title' => sprintf('PR from %s to %s', $this->getHeadBranch(), $this->getBaseBranch()),
            'body' => $this->buildPullRequestBody($releaseGroups),
        ];

        return $this->provider->createPullRequest($params);
    }

    /**
     * @param array<string> $releaseGroups
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection
     */
    public function save(array $releaseGroups): VcsResponseCollection
    {
        $collection = new VcsResponseCollection();
        $response = $this->createBranch($this->getHeadBranch());
        $collection->add($response);
        if (!$response->isSuccess()) {
            return $collection;
        }
        $response = $this->addChanges();
        $collection->add($response);
        if (!$response->isSuccess()) {
            return $collection;
        }
        $response = $this->commitChanges(
            sprintf('Installed: %s %s', PHP_EOL, implode(PHP_EOL, $releaseGroups)),
        );
        $collection->add($response);
        if (!$response->isSuccess()) {
            return $collection;
        }
        $response = $this->pushChanges($this->getHeadBranch());
        $collection->add($response);
        if (!$response->isSuccess()) {
            return $collection;
        }
        $collection->add($this->createPullRequest($releaseGroups));

        return $collection;
    }

    /**
     * @return string
     */
    public function getCommitHash(): string
    {
        if (!$this->commitHash) {
            $command = ['git', 'rev-parse', 'HEAD'];
            $process = $this->runProcess($command);
            $this->commitHash = trim($process->getOutput());
        }

        return $this->commitHash;
    }

    /**
     * @return string
     */
    public function getBaseBranch(): string
    {
        if (!$this->baseBranch) {
            $command = ['git', 'rev-parse', '--abbrev-ref', 'HEAD'];
            $process = $this->runProcess($command);
            $this->baseBranch = trim($process->getOutput());
        }

        return $this->baseBranch;
    }

    /**
     * @return string
     */
    public function getHeadBranch(): string
    {
        return sprintf(static::BRANCH_TEMPLATE, $this->getBaseBranch(), $this->getCommitHash());
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
     * @return string
     */
    protected function getRemote()
    {
        return sprintf(
            'https://%s@github.com/%s/%s.git',
            $this->config->getGithubAccessToken(),
            $this->config->getGithubOrganization(),
            $this->config->getGithubRepository(),
        );
    }

    /**
     * @param array<string> $releaseGroups
     *
     * @return string
     */
    protected function buildPullRequestBody(array $releaseGroups): string
    {
        $releaseGroups = array_map(function ($output) {
            return '* ' . $output;
        }, $releaseGroups);
        $releaseGroupList = implode("\n", $releaseGroups);

        $text = <<<TXT
Auto created via Upgrader tool.

#### Overview

**Packages upgraded:**
$releaseGroupList

TXT;

        return $text;
    }

    /**
     * @param array<string> $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function runProcess(array $command): Process
    {
        $process = new Process($command, (string)getcwd());
        $process->setTimeout($this->config->getCommandExecutionTimeout());
        $process->run();

        return $process;
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     * @param string|null $commandOutput
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    protected function createResponseForProcess(Process $process, ?string $commandOutput = null): VcsResponse
    {
        $command = $commandOutput ?: str_replace('\'', '', $process->getCommandLine());
        $output = $process->getExitCode() ? $process->getErrorOutput() : '';
        $outputs = array_filter([$command, $output]);

        return $this->createResponse($process->isSuccessful(), implode(PHP_EOL, $outputs));
    }

    /**
     * @param bool $isSuccess
     * @param string $output
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    protected function createResponse($isSuccess, $output): VcsResponse
    {
        return new VcsResponse($isSuccess, $output);
    }
}
