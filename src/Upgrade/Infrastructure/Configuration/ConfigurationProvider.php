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
        return (string)getenv('UPGRADE_STRATEGY') ?: static::COMPOSER_STRATEGY;
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
}
