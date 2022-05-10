<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Configuration;

use Upgrade\Application\Provider\ConfigurationProviderInterface;

class ConfigurationProvider implements \Upgrade\Application\Provider\ConfigurationProviderInterface
{
    /**
     * @var string
     */
    protected const DEFAULT_BRANCH_PATTERN = 'upgradebot/upgrade-for-%s-%s';

    /**
     * @var bool
     */
    protected const DEFAULT_IS_PR_AUTO_MERGE_ENABLED = false;

    /**
     * @var int
     */
    public const DEFAULT_SOFT_THRESHOLD_BUGFIX = 30;

    /**
     * @var int
     */
    public const DEFAULT_SOFT_THRESHOLD_MINOR = 10;

    /**
     * @var int
     */
    public const DEFAULT_SOFT_THRESHOLD_MAJOR = 0;

    /**
     * @var int
     */
    public const DEFAULT_THRESHOLD_RELEASE_GROUP = 30;

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
    public function getReleaseGroupRequireProcessor(): string
    {
        return (string)getenv('RELEASE_GROUP_REQUIRE_PROCESSOR') ?: static::AGGREGATE_RELEASE_GROUP_REQUIRE_PROCESSOR;
    }

    /**
     * @return string
     */
    public function getBranchPattern(): string
    {
        return (string)getenv('BRANCH_PATTERN') ?: static::DEFAULT_BRANCH_PATTERN;
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
        return (bool)getenv('IS_PR_AUTO_MERGE_ENABLED') ?: static::DEFAULT_IS_PR_AUTO_MERGE_ENABLED;
    }

    /**
     * @return string
     */
    public function getSourceCodeProvider(): string
    {
        return (string)getenv('SOURCE_CODE_PROVIDER') ?: static::GITHUB_SOURCE_CODE_PROVIDER;
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
     * @return int
     */
    public function getSoftThresholdBugfix(): int
    {
        return (int)getenv('SOFT_THRESHOLD_BUGFIX') ?: static::DEFAULT_SOFT_THRESHOLD_BUGFIX;
    }

    /**
     * @return int
     */
    public function getSoftThresholdMinor(): int
    {
        return (int)getenv('SOFT_THRESHOLD_MINOR') ?: static::DEFAULT_SOFT_THRESHOLD_MINOR;
    }

    /**
     * @return int
     */
    public function getSoftThresholdMajor(): int
    {
        return (int)getenv('SOFT_THRESHOLD_MAJOR') ?: static::DEFAULT_SOFT_THRESHOLD_MAJOR;
    }

    /**
     * @return int
     */
    public function getThresholdReleaseGroup(): int
    {
        return (int)getenv('THRESHOLD_RELEASE_GROUP') ?: static::DEFAULT_THRESHOLD_RELEASE_GROUP;
    }
}
