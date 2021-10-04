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
    public function add(): VcsResponse
    {
        $command = ['git', 'add', 'composer.lock', 'composer.json'];
        $process = $this->runProcess($command);

        return $this->createResponse($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function branch(string $branch): VcsResponse
    {
        $command = ['git', 'checkout', '-b', $branch];
        $process = $this->runProcess($command);

        return $this->createResponse($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function checkout(string $branch): VcsResponse
    {
        $command = ['git', 'checkout', $branch];
        $process = $this->runProcess($command);

        return $this->createResponse($process);
    }

    /**
     * @param string $message
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function commit(string $message): VcsResponse
    {
        $command = ['git', 'commit', '-m', $message];
        $process = $this->runProcess($command);

        return $this->createResponse($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function push(string $branch): VcsResponse
    {
        $remote = sprintf(
            "https://%s@github.com/%s/%s.git",
            $this->config->getGithubAccessToken(),
            $this->config->getGithubOrganization(),
            $this->config->getGithubRepository()
        );
        $command = ['git', 'push', '--set-upstream', $remote, $branch];
        $process = $this->runProcess($command);

        return $this->createResponse($process);
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function check(): VcsResponse
    {
        $command = ['git', 'update-index', '--refresh'];
        $process = $this->runProcess($command);

        return $this->createResponse($process);
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
        $response = $this->branch($this->getHeadBranch());
        $collection->add($response);
        if (!$response->isSuccess()) {
            return $collection;
        }
        $response = $this->add();
        $collection->add($response);
        if (!$response->isSuccess()) {
            return $collection;
        }
        $response = $this->commit(
            sprintf('Installed: %s %s', PHP_EOL, implode(PHP_EOL, $releaseGroups))
        );
        $collection->add($response);
        if (!$response->isSuccess()) {
            return $collection;
        }
        $response = $this->push($this->getHeadBranch());
        $collection->add($response);
        if (!$response->isSuccess()) {
            return $collection;
        }
        $collection->add($this->createPullRequest($releaseGroups));
        $collection->add($this->checkout($this->getBaseBranch()));

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
     * @param array $releaseGroups
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

**Release Groups upgraded:**
$releaseGroupList

TXT;

        return $text;
    }

    /**
     * @param array $command
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
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    protected function createResponse(Process $process): VcsResponse
    {
        $command = str_replace('\'', '', $process->getCommandLine());
        $output = $process->getExitCode() ? $process->getErrorOutput() : '';
        $outputs = array_filter([$command, $output]);

        return new VcsResponse($process->isSuccessful(), implode(PHP_EOL, $outputs));
    }
}
