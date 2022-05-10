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
    public const SEQUENTIAL_RELEASE_GROUP_REQUIRE_PROCESSOR = 'sequential-release-group-require-processor';

    /**
     * @var string
     */
    public const AGGREGATE_RELEASE_GROUP_REQUIRE_PROCESSOR = 'aggregate-release-group-require-processor';

    /**
     * @return string
     */
    public function getUpgradeStrategy(): string;

    /**
     * @return string
     */
    public function getReleaseGroupRequireProcessor(): string;

    /**
     * @return int
     */
    public function getSoftThresholdBugfix(): int;

    /**
     * @return int
     */
    public function getSoftThresholdMinor(): int;

    /**
     * @return int
     */
    public function getSoftThresholdMajor(): int;

    /**
     * @return int
     */
    public function getThresholdReleaseGroup(): int;
}
