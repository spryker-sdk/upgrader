<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Configuration;

use Upgrade\Application\Provider\ConfigurationProviderInterface;

class ConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * @var string
     */
    protected const DEFAULT_BRANCH_PATTERN = 'upgradebot/upgrade-for-%s-%s';

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
     * Specification:
     * - Defines access token for code source provider system.
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return (string)getenv('ACCESS_TOKEN');
    }

    /**
     * Specification:
     * - Returns the link to the source code provider.
     *
     * @return string
     */
    public function getSourceCodeProviderUrl(): string
    {
        return (string)getenv('SOURCE_CODE_PROVIDER_URL');
    }

    /**
     * Specification:
     * - Defines organisation name for source provider.
     *
     * @return string
     */
    public function getOrganizationName(): string
    {
        return (string)getenv('ORGANIZATION_NAME');
    }

    /**
     * Specification:
     * - Defines repository name for your project.
     *
     * @return string
     */
    public function getRepositoryName(): string
    {
        return (string)getenv('REPOSITORY_NAME');
    }

    /**
     * Specification:
     * - Defines id of your GitLab project.
     *
     * @return string
     */
    public function getGitLabProjectId(): string
    {
        return (string)getenv('GITLAB_PROJECT_ID');
    }

    /**
     * Specification:
     * - Defines delay in seconds between request for PR creation and enable auto merging.
     *
     * @return int
     */
    public function getGitLabDelayBetweenPrCreatingAndMerging(): int
    {
        return (int)getenv('GITLAB_DELAY_BETWEEN_PR_CREATING_AND_MERGING') ?: static::GITLAB_DELAY_BETWEEN_PR_CREATING_AND_MERGING;
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
