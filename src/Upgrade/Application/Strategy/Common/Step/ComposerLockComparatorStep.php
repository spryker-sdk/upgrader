<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\StepInterface;

class ComposerLockComparatorStep implements StepInterface
{
    protected PackageManagerAdapterInterface $packageManager;

    public function __construct(PackageManagerAdapterInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $composerLockDiffDto = $this->packageManager->getComposerLockDiff();

        $existedDiffDto = $stepsExecutionDto->getComposerLockDiff();
        if (!$existedDiffDto) {
            return $stepsExecutionDto->setComposerLockDiff($composerLockDiffDto);
        }

        return $stepsExecutionDto->setComposerLockDiff(
            new ComposerLockDiffDto(
                [...$existedDiffDto->getRequiredPackages(), ...$composerLockDiffDto->getRequiredPackages()],
                [...$existedDiffDto->getRequiredDevPackages(), ...$composerLockDiffDto->getRequiredDevPackages()],
            ),
        );
    }
}
