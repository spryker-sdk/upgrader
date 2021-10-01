<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem;

use Symfony\Component\Process\Process;
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
    protected $basicBranch;

    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    /**
     * @param \Upgrader\UpgraderConfig $config
     */
    public function __construct(UpgraderConfig $config)
    {
        $this->config = $config;
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
        $command = ['git', 'push', '--set-upstream', 'origin', $branch];
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
     * @return \Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection
     */
    public function save(array $releaseGroups): VcsResponseCollection
    {
        $collection = new VcsResponseCollection();
        $response = $this->branch($this->getBranch());
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
        $response = $this->push($this->getBranch());
        $collection->add($response);
        if (!$response->isSuccess()) {
            return $collection;
        }
        $collection->add($this->checkout($this->getBasicBranch()));

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
    public function getBasicBranch(): string
    {
        if (!$this->basicBranch) {
            $command = ['git', 'rev-parse', '--abbrev-ref', 'HEAD'];
            $process = $this->runProcess($command);
            $this->basicBranch = trim($process->getOutput());
        }

        return $this->basicBranch;
    }

    /**
     * @return string
     */
    public function getBranch(): string
    {
        return sprintf(static::BRANCH_TEMPLATE, $this->getBasicBranch(), $this->getCommitHash());
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
