<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

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
    public const SEQUENTIAL_RELEASE_GROUP_REQUIRE_PROCESSOR = 'sequential';

    /**
     * @var string
     */
    public const AGGREGATE_RELEASE_GROUP_REQUIRE_PROCESSOR = 'aggregate';

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
     * - Defines package require mode.
     * - Possible mode: sequential and aggregate (default).
     *
     * @return string
     */
    public function getReleaseGroupRequireProcessor(): string;

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
}
