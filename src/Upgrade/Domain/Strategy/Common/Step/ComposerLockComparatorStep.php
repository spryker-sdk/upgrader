<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\Common\Step;

use PackageManager\Application\Service\PackageManagerService;
use Upgrade\Domain\Adapter\PackageManagerAdapterInterface;
use Upgrade\Domain\Dto\Step\StepsExecutionDto;
use Upgrade\Domain\Strategy\StepInterface;

class ComposerLockComparatorStep implements StepInterface
{
    /**
     * @var PackageManagerAdapterInterface|PackageManagerService
     */
    protected PackageManagerAdapterInterface $packageManagerService;

    /**
     * @param PackageManagerAdapterInterface $composerLockComparator
     */
    public function __construct(PackageManagerAdapterInterface $composerLockComparator)
    {
        $this->packageManagerService = $composerLockComparator;
    }

    /**
     * @param \Upgrade\Domain\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $composerLockDiffDto = $this->packageManagerService->getComposerLockDiff();

        if ($composerLockDiffDto->isEmpty()) {
            return $stepsExecutionDto
                ->setIsSuccessful(false)
                ->addOutputMessage('The branch is up to date. No further action is required.');
        }

        return $stepsExecutionDto->addComposerLockDiff($composerLockDiffDto);
    }
}
