<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Configuration;

class ConfigurationProvider
{
    /**
     * @var string
     */
    public const COMPOSER_STRATEGY = 'composer';

    /**
     * @var string
     */
    public const RELEASE_APP_STRATEGY = 'release-app';

    /**
     * @var string
     */
    public const GITHUB_SOURCE_CODE_PROVIDER = 'github';

    /**
     * @var string
     */
    public const VCS_TYPE = 'git';

    /**
     * @var string
     */
    protected const BRANCH_PATTERN = 'upgradebot/upgrade-for-%s-%s';

    /**
     * @var bool
     */
    protected const IS_PR_AUTO_MERGE_ENABLED = false;

    /**
     * @return string
     */
    public function getUpgradeStrategy(): string
    {
        return (string)getenv('UPGRADE_STRATEGY') ?: static::RELEASE_APP_STRATEGY;
    }

    /**
     * @return string
     */
    public function getSourceCodeProvider(): string
    {
        return (string)getenv('SOURCE_CODE_PROVIDER') ?: static::GITHUB_SOURCE_CODE_PROVIDER;
    }

    /**
     * @return string
     */
    public function getBranchPattern(): string
    {
        return (string)getenv('BRANCH_PATTERN') ?: static::BRANCH_PATTERN;
    }

    /**
     * @return string
     */
    public function getCommitMessage(): string
    {
        return (string)getenv('COMMIT_MESSAGE') ?: 'PR is create by the Spryker Upgrader Bot';
    }

    /**
     * @return bool
     */
    public function isPullRequestAutoMergeEnabled(): bool
    {
        return (bool)getenv('IS_PR_AUTO_MERGE_ENABLED') ?: static::IS_PR_AUTO_MERGE_ENABLED;
    }

    /**
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return (string)getenv('ACCESS_TOKEN');
    }

    /**
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getOrganizationName(): string
    {
        return (string)getenv('ORGANIZATION_NAME');
    }

    /**
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getRepositoryName(): string
    {
        return (string)getenv('REPOSITORY_NAME');
    }

    /**
     * @var string
     */
    protected const UPGRADER_RELEASE_APP_URL = 'UPGRADER_RELEASE_APP_URL';

    /**
     * @var string
     */
    protected const DEFAULT_RELEASE_APP_URL = 'https://api.release.spryker.com';

    /**
     * @return string
     */
    public function getReleaseAppUrl(): string
    {
        return (string)getenv(static::UPGRADER_RELEASE_APP_URL) ?: static::DEFAULT_RELEASE_APP_URL;
    }

    /**
     * @var string
     */
    protected const UPGRADER_HTTP_RETRIEVE_ATTEMPTS_COUNT = 'UPGRADER_HTTP_RETRIEVE_ATTEMPTS_COUNT';

    /**
     * @var int
     */
    protected const DEFAULT_HTTP_RETRIEVE_ATTEMPTS_COUNT = 5;

    /**
     * @var string
     */
    protected const UPGRADER_HTTP_RETRIEVE_RETRY_DELAY = 'UPGRADER_HTTP_RETRIEVE_RETRY_DELAY';

    /**
     * @var int
     */
    protected const DEFAULT_HTTP_RETRIEVE_RETRY_DELAY = 10;

    /**
     * @return int
     */
    public function getHttpRetrieveAttemptsCount(): int
    {
        return (int)getenv(static::UPGRADER_HTTP_RETRIEVE_ATTEMPTS_COUNT) ?: static::DEFAULT_HTTP_RETRIEVE_ATTEMPTS_COUNT;
    }

    /**
     * @return int
     */
    public function getHttpRetrieveRetryDelay(): int
    {
        return (int)getenv(static::UPGRADER_HTTP_RETRIEVE_RETRY_DELAY) ?: static::DEFAULT_HTTP_RETRIEVE_RETRY_DELAY;
    }

    /**
     * @var string
     */
    protected const UPGRADER_COMMAND_EXECUTION_TIMEOUT = 'UPGRADER_COMMAND_EXECUTION_TIMEOUT';

    /**
     * @var int
     */
    protected const DEFAULT_COMMAND_EXECUTION_TIMEOUT = 600;

    /**
     * @return int
     */
    public function getCommandExecutionTimeout(): int
    {
        return (int)getenv(static::UPGRADER_COMMAND_EXECUTION_TIMEOUT) ?: static::DEFAULT_COMMAND_EXECUTION_TIMEOUT;
    }
}
