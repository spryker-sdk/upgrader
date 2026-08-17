<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Provider;

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
    public const SEQUENTIAL_RELEASE_GROUP_PROCESSOR = 'sequential';

    /**
     * Specification:
     * - Defines upgrade strategy.
     * - Possible strategies: composer and release-app (default).
     */
    public function getUpgradeStrategy(): string;

    /**
     * Specification:
     * - Defines install composer package strategy.
     * - Possible strategies: true (update only composer.lock) and false (with package installation by default).
     */
    public function getComposerNoInstall(): bool;

    /**
     * Specification:
     * - Defines trigger Integrator command `integrator:manifest:run`
     */
    public function isIntegratorEnabled(): bool;

    public function isEvaluatorEnabled(): bool;

    /**
     * Specification:
     * - Defines package require mode.
     * - Possible mode: sequential (default).
     */
    public function getReleaseGroupProcessor(): string;

    /**
     * Specification:
     * - Defines soft threshold for bugfixes.
     */
    public function getSoftThresholdPatch(): int;

    /**
     * Specification:
     * - Defines soft threshold for minor.
     */
    public function getSoftThresholdMinor(): int;

    /**
     * Specification:
     * - Defines soft threshold for major.
     */
    public function getSoftThresholdMajor(): int;

    /**
     * Specification:
     * - Defines threshold for release groups.
     */
    public function getThresholdReleaseGroup(): int;

    /**
     * Specification:
     * - Defines application env.
     */
    public function getAppEnv(): string;

    /**
     * Specification:
     * - Defines report sending availability.
     */
    public function isReportingEnabled(): bool;

    /**
     * Specification:
     * - Defines has Upgrader install new packages in the release group.
     */
    public function isPackageUpgradeOnly(): bool;

    /**
     * Specification:
     *  - Defines the specific release group id.
     */
    public function getReleaseGroupId(): ?int;

    /**
     * Specification:
     * - Defines the report request auth token.
     */
    public function getReportSendAuthToken(): string;

    /**
     * Specification:
     * - Defines Buddy run branch.
     */
    public function getBuddyRunBranch(): string;

    /**
     * Specification:
     *  - Defines manifest rating threshold.
     */
    public function getManifestsRatingThreshold(): int;

    /**
     * Specification:
     *  - Defines
     *
     * @return array<string>
     */
    public function getPullRequestReviewers(): array;

    /**
     * Specification:
     * - Defines whether to run PHPStan per directory or analyze all files at once.
     */
    public function isPhpStanOptimizationRun(): bool;

    /**
     * Specification:
     * - Defines whether Dynamic Multistore feature is enabled in Spryker.
     */
    public function isSprykerDynamicStoreModeEnabled(): bool;

    /**
     * Specification:
     * - Defines whether error traces in error messages should be truncated before adding them to a PR.
     */
    public function isTruncateErrorTracesInPrsEnabled(): bool;
}
