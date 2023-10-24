<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Composer\Fixer;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\FixerStepInterface;

abstract class AbstractFeaturePackageFixerStep implements FixerStepInterface
{
    /**
     * @var string
     */
    protected const KEY_FEATURES = 'features';

    /**
     * @var string
     */
    protected const FEATURE_PACKAGE_PATTERN = '/(?<' . self::KEY_FEATURES . '>spryker-feature\/[-\w]+).+conflicts.+/';

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(PackageManagerAdapterInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return bool
     */
    public function isApplicable(StepsResponseDto $stepsExecutionDto): bool
    {
        return !$stepsExecutionDto->getIsSuccessful() &&
            $stepsExecutionDto->getOutputMessage() !== null &&
            preg_match(static::FEATURE_PACKAGE_PATTERN, $stepsExecutionDto->getOutputMessage());
    }
}
