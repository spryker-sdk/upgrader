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
    public const GITLAB_SOURCE_CODE_PROVIDER = 'gitlab';

    /**
     * @var int
     */
    public const GITLAB_DELAY_BETWEEN_PR_CREATING_AND_MERGING = 20;

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
     * Specification:
     * - Defines upgrade strategy.
     * - Possible strategies: composer and release-app (default).
     *
     * @return string
     */
    public function getUpgradeStrategy(): string
    {
        return (string)getenv('UPGRADE_STRATEGY') ?: static::COMPOSER_STRATEGY;
    }

    /**
     * Specification:
     * - Defines the default source code provider.
     * - Available options: GitHub (default) and GitLab.
     *
     * @return string
     */
    public function getSourceCodeProvider(): string
    {
        return (string)getenv('SOURCE_CODE_PROVIDER') ?: static::GITHUB_SOURCE_CODE_PROVIDER;
    }

    /**
     * Specification:
     * - Defines pattern for branch that will be created during upgrade process.
     *
     * @return string
     */
    public function getBranchPattern(): string
    {
        return (string)getenv('BRANCH_PATTERN') ?: static::BRANCH_PATTERN;
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
        return (bool)getenv('IS_PR_AUTO_MERGE_ENABLED') ?: static::IS_PR_AUTO_MERGE_ENABLED;
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
}
