<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Composer\Fixer;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Strategy\UpgradeFixerInterface;

abstract class AbstractFeaturePackageUpgradeFixer implements UpgradeFixerInterface
{
    /**
     * @var bool
     */
    protected const RE_RUN_STEP = true;

    /**
     * @var string
     */
    protected const KEY_FEATURES = 'features';

    /**
     * @var string
     */
    protected const FEATURE_PACKAGE_PATTERN = '/(?<' . self::KEY_FEATURES . '>spryker-feature\/[-\w]+).+conflicts.+/';

    protected PackageManagerAdapterInterface $packageManager;

    public function __construct(PackageManagerAdapterInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    public function isApplicable(ReleaseGroupDto $releaseGroup, PackageManagerResponseDto $packageManagerResponseDto): bool
    {
        return !$packageManagerResponseDto->isSuccessful() &&
            $packageManagerResponseDto->getOutputMessage() !== null &&
            preg_match(static::FEATURE_PACKAGE_PATTERN, $packageManagerResponseDto->getOutputMessage());
    }

    public function isReRunStep(): bool
    {
        return static::RE_RUN_STEP;
    }
}
