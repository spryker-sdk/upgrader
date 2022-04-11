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
     * The method define strategy for project upgrades.
     * Available two option: composer strategy (default) and release-app strategy.
     *
     * @return string
     */
    public function getUpgradeStrategy(): string
    {
        return (string)getenv('UPGRADE_STRATEGY') ?: static::COMPOSER_STRATEGY;
    }

    /**
     * The method define source code provider for upgrade process.
     * Available GitHub (default) and GitLab code source providers.
     *
     * @return string
     */
    public function getSourceCodeProvider(): string
    {
        return (string)getenv('SOURCE_CODE_PROVIDER') ?: static::GITHUB_SOURCE_CODE_PROVIDER;
    }

    /**
     * The method define pattern for branch that will be created during upgrade process.
     *
     * @return string
     */
    public function getBranchPattern(): string
    {
        return (string)getenv('BRANCH_PATTERN') ?: static::BRANCH_PATTERN;
    }

    /**
     * The method define commit message that will be used during upgrade process.
     *
     * @return string
     */
    public function getCommitMessage(): string
    {
        return (string)getenv('COMMIT_MESSAGE') ?: 'PR is create by the Spryker Upgrader Bot';
    }

    /**
     * The method define whether the changes will be automatically applied after the upgrade process.
     *
     * @return bool
     */
    public function isPullRequestAutoMergeEnabled(): bool
    {
        return (bool)getenv('IS_PR_AUTO_MERGE_ENABLED') ?: static::IS_PR_AUTO_MERGE_ENABLED;
    }

    /**
     * The method return access token for code source provider system.
     *
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return (string)getenv('ACCESS_TOKEN');
    }

    /**
     * The method return link to your own code source provider system.
     * By default left it empty.
     *
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getSourceCodeProviderUrl(): string
    {
        return (string)getenv('SOURCE_CODE_PROVIDER_URL');
    }

    /**
     * The method return GitHub organization name for your project.
     * Define ORGANIZATION_NAME environment variable if you use GitHub source code provider.
     *
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getOrganizationName(): string
    {
        return (string)getenv('ORGANIZATION_NAME');
    }

    /**
     * The method return GitHub repository name for your project.
     * Define REPOSITORY_NAME environment variable if you use GitHub source code provider.
     *
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getRepositoryName(): string
    {
        return (string)getenv('REPOSITORY_NAME');
    }

    /**
     * The method return id of your GitLab project.
     * Define GITLAB_PROJECT_ID environment variable if you use GitLab source code provider.
     *
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getGitLabProjectId(): string
    {
        return (string)getenv('GITLAB_PROJECT_ID');
    }

    /**
     * The method return delay in seconds between request for PR creation and enable auto merging if the option is enabled.
     *
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return int
     */
    public function getGitLabDelayBetweenPrCreatingAndMerging(): int
    {
        return (int)getenv('GITLAB_DELAY_BETWEEN_PR_CREATING_AND_MERGING') ?: static::GITLAB_DELAY_BETWEEN_PR_CREATING_AND_MERGING;
    }
}
