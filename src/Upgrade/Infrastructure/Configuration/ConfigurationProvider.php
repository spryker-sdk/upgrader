<?php

declare(strict_types=1);

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
    public const GITHUB_SOURCE_CODE_PROVIDER = 'github';

    /**
     * @var string
     */
    public const GITLAB_SOURCE_CODE_PROVIDER = 'gitlab';

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
     * @var int
     */
    public const GITLAB_DELAY_BETWEEN_PR_CREATING_AND_MERGING = 20;

    /**
     * @var string
     */
    protected const DEFAULT_BRANCH_PATTERN = 'upgradebot/upgrade-for-%s-%s';

    /**
     * @var bool
     */
    protected const DEFAULT_IS_PR_AUTO_MERGE_ENABLED = false;

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getUpgradeStrategy(): string
    {
        return (string)getenv('UPGRADE_STRATEGY') ?: static::RELEASE_APP_STRATEGY;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getReleaseGroupProcessor(): string
    {
        return (string)getenv('RELEASE_GROUP_PROCESSOR') ?: static::AGGREGATE_RELEASE_GROUP_PROCESSOR;
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
     * - Defines pattern for branch that will be created during upgrade process.
     *
     * @return string
     */
    public function getBranchPattern(): string
    {
        return (string)getenv('BRANCH_PATTERN') ?: static::DEFAULT_BRANCH_PATTERN;
    }

    /**
     * Specification:
     * - Defines commit message that will be used during upgrade process.
     *
     * @return string
     */
    public function getCommitMessage(): string
    {
        return (string)getenv('COMMIT_MESSAGE') ?: 'PR is create by the Spryker Upgrader Bot';
    }

    /**
     * Specification:
     * - Defines if pull request auto-merge is enabled. Default value is false.
     *
     * @return bool
     */
    public function isPullRequestAutoMergeEnabled(): bool
    {
        return (bool)getenv('IS_PR_AUTO_MERGE_ENABLED') ?: static::DEFAULT_IS_PR_AUTO_MERGE_ENABLED;
    }

    /**
     * Specification:
     * - Returns the link to the source code provider.
     *
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
     * {@inheritDoc}
     *
     * @return int
     */
    public function getSoftThresholdBugfix(): int
    {
        return (int)getenv('SOFT_THRESHOLD_BUGFIX') ?: static::DEFAULT_SOFT_THRESHOLD_BUGFIX;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getSoftThresholdMinor(): int
    {
        return (int)getenv('SOFT_THRESHOLD_MINOR') ?: static::DEFAULT_SOFT_THRESHOLD_MINOR;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getSoftThresholdMajor(): int
    {
        return (int)getenv('SOFT_THRESHOLD_MAJOR') ?: static::DEFAULT_SOFT_THRESHOLD_MAJOR;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getThresholdReleaseGroup(): int
    {
        return (int)getenv('THRESHOLD_RELEASE_GROUP') ?: static::DEFAULT_THRESHOLD_RELEASE_GROUP;
    }
}
