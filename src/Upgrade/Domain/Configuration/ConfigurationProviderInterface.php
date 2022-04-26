<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Configuration;

interface ConfigurationProviderInterface
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
    public const SEQUENTIAL_RELEASE_GROUP_REQUIRE_PROCESSOR = 'sequential-release-group-require-processor';

    /**
     * @var string
     */
    public const AGGREGATE_RELEASE_GROUP_REQUIRE_PROCESSOR = 'aggregate-release-group-require-processor';

    /**
     * @var string
     */
    public const GITHUB_SOURCE_CODE_PROVIDER = 'github';

    /**
     * @var string
     */
    public const VCS_TYPE = 'git';

    /**
     * @return string
     */
    public function getUpgradeStrategy(): string;

    /**
     * @return string
     */
    public function getReleaseGroupRequireProcessor(): string;

    /**
     * @return string
     */
    public function getSourceCodeProvider(): string;

    /**
     * @return string
     */
    public function getBranchPattern(): string;

    /**
     * @return string
     */
    public function getCommitMessage(): string;

    /**
     * @return bool
     */
    public function isPullRequestAutoMergeEnabled(): bool;

    /**
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getAccessToken(): string;

    /**
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getOrganizationName(): string;

    /**
     * @throw \Upgrade\Infrastructure\Exception\EnvironmentVariableIsNotDefinedException
     *
     * @return string
     */
    public function getRepositoryName(): string;

    /**
     * @return int
     */
    public function getSoftThresholdBugfixAmount(): int;

    /**
     * @return int
     */
    public function getSoftThresholdMinorAmount(): int;

    /**
     * @return int
     */
    public function getSoftThresholdMajorAmount(): int;

    /**
     * @return int
     */
    public function getThresholdReleaseGroupAmount(): int;
}
