<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader;

use RuntimeException;
use Symfony\Component\Process\Process;

class UpgraderConfig
{
    protected const UPGRADER_RELEASE_APP_URL = 'UPGRADER_RELEASE_APP_URL';
    protected const DEFAULT_RELEASE_APP_URL = 'https://api.release.spryker.com';
    protected const UPGRADER_COMMAND_EXECUTION_TIMEOUT = 'UPGRADER_COMMAND_EXECUTION_TIMEOUT';
    protected const DEFAULT_COMMAND_EXECUTION_TIMEOUT = 600;
    protected const GITHUB_ACCESS_TOKEN = 'GITHUB_ACCESS_TOKEN';
    protected const GITHUB_ORGANIZATION = 'GITHUB_ORGANIZATION';
    protected const GITHUB_REPOSITORY = 'GITHUB_REPOSITORY';

    /**
     * @var string|null
     */
    protected $previousCommitHash;

    /**
     * @var string|null
     */
    protected $startingBranch;

    /**
     * @throws \RuntimeException
     *
     * @return string
     */
    public function getGithubAccessToken(): string
    {
        $token = getenv(static::GITHUB_ACCESS_TOKEN);
        if (!$token) {
            throw new RuntimeException(sprintf('Please set %s value.', static::GITHUB_ACCESS_TOKEN));
        }

        return $token;
    }

    /**
     * @throws \RuntimeException
     *
     * @return string
     */
    public function getGithubOrganization(): string
    {
        $organization = getenv(static::GITHUB_ORGANIZATION);
        if (!$organization) {
            throw new RuntimeException(sprintf('Please set %s value.', static::GITHUB_ORGANIZATION));
        }

        return $organization;
    }

    /**
     * @throws \RuntimeException
     *
     * @return string
     */
    public function getGithubRepository(): string
    {
        $repository = getenv(static::GITHUB_REPOSITORY);
        if (!$repository) {
            throw new RuntimeException(sprintf('Please set %s value.', static::GITHUB_REPOSITORY));
        }

        return $repository;
    }

    /**
     * @return int
     */
    public function getCommandExecutionTimeout(): int
    {
        return (int)getenv(static::UPGRADER_COMMAND_EXECUTION_TIMEOUT) ?: static::DEFAULT_COMMAND_EXECUTION_TIMEOUT;
    }

    /**
     * @return string
     */
    public function getReleaseAppUrl(): string
    {
        return (string)getenv(static::UPGRADER_RELEASE_APP_URL) ?: static::DEFAULT_RELEASE_APP_URL;
    }

    /**
     * @return string
     */
    public function getPreviousCommitHash(): string
    {
        if (!$this->previousCommitHash) {
            $process = new Process(explode(' ', 'git rev-parse HEAD'), (string)getcwd());
            $process->run();
            $this->previousCommitHash = trim($process->getOutput());
        }

        return $this->previousCommitHash;
    }

    /**
     * @return string
     */
    public function getStartingBranch(): string
    {
        if (!$this->startingBranch) {
            $process = new Process(explode(' ', 'git rev-parse --abbrev-ref HEAD'), (string)getcwd());
            $process->run();

            $this->startingBranch = trim($process->getOutput());
        }

        return $this->startingBranch;
    }

    /**
     * @return string
     */
    public function getPrBranch(): string
    {
        return sprintf('upgradebot/upgrade-for-%s-%s', $this->getStartingBranch(), $this->getPreviousCommitHash());
    }
}
