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
        $process = $this->runProcess('git add composer.lock composer.json');

        return $this->createResponse($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function branch(string $branch): VcsResponse
    {
        $process = $this->runProcess(sprintf('git checkout -b %s', $branch));

        return $this->createResponse($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function checkout(string $branch): VcsResponse
    {
        $process = $this->runProcess(sprintf('git checkout %s', $branch));

        return $this->createResponse($process);
    }

    /**
     * @param string $message
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function commit(string $message): VcsResponse
    {
        $process = $this->runProcess('git commit -m $message');

        return $this->createResponse($process);
    }

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function push(string $branch): VcsResponse
    {
        $process = $this->runProcess(sprintf('git push --set-upstream origin %s', $branch));

        return $this->createResponse($process);
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function check(): VcsResponse
    {
        $process = $this->runProcess('git update-index --refresh');

        return $this->createResponse($process);
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection
     */
    public function save(): VcsResponseCollection
    {
        $collection = new VcsResponseCollection();
        $collection->add($this->branch($this->getBranch()));
        $collection->add($this->add());
        $collection->add($this->commit('some message'));
        $collection->add($this->push($this->getBranch()));
        $collection->add($this->checkout($this->getBasicBranch()));

        return $collection;
    }

    /**
     * @return string
     */
    public function getCommitHash(): string
    {
        if (!$this->commitHash) {
            $process = $this->runProcess('git rev-parse HEAD');
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
            $process = $this->runProcess('git rev-parse --abbrev-ref HEAD');
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
     * @param string $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function runProcess(string $command): Process
    {
        $process = new Process(explode(' ', $command), (string)getcwd());
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
