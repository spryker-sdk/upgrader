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
     * @var string
     */
    public const AGGREGATE_RELEASE_GROUP_PROCESSOR = 'aggregate';

    /**
     * Specification:
     * - Defines upgrade strategy.
     * - Possible strategies: composer and release-app (default).
     *
     * @return string
     */
    public function getUpgradeStrategy(): string;

    /**
     * Specification:
     * - Defines install composer package strategy.
     * - Possible strategies: true (update only composer.lock) and false (with package installation by default).
     *
     * @return bool
     */
    public function getComposerNoInstall(): bool;

    /**
     * Specification:
     * - Defines trigger Integrator command `integrator:manifest:run`
     *
     * @return bool
     */
    public function isIntegratorEnabled(): bool;

    /**
     * Specification:
     * - Defines package require mode.
     * - Possible mode: sequential and aggregate (default).
     *
     * @return string
     */
    public function getReleaseGroupProcessor(): string;

    /**
     * Specification:
     * - Defines soft threshold for bugfixes.
     *
     * @return int
     */
    public function getSoftThresholdBugfix(): int;

    /**
     * Specification:
     * - Defines soft threshold for minor.
     *
     * @return int
     */
    public function getSoftThresholdMinor(): int;

    /**
     * Specification:
     * - Defines soft threshold for major.
     *
     * @return int
     */
    public function getSoftThresholdMajor(): int;

    /**
     * Specification:
     * - Defines threshold for release groups.
     *
     * @return int
     */
    public function getThresholdReleaseGroup(): int;

    /**
     * Specification:
     * - Defines execution env.
     *
     * @return string
     */
    public function getExecutionEnv(): string;

    /**
     * Specification:
     * - Defines report sending availability.
     *
     * @return bool
     */
    public function isReportingEnabled(): bool;

    /**
     * Specification:
     * - Defines the report request auth token.
     *
     * @return string
     */
    public function getReportSendAuthToken(): string;
}
