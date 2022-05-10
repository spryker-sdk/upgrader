<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Bridge\PackageManagerBridgeInterface;
use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Application\Strategy\StepInterface;

class ComposerLockComparatorStep implements StepInterface
{
    /**
     * @var \Upgrade\Application\Bridge\PackageManagerBridgeInterface
     */
    protected PackageManagerBridgeInterface $packageManager;

    /**
     * @param \Upgrade\Application\Bridge\PackageManagerBridgeInterface $packageManager
     */
    public function __construct(PackageManagerBridgeInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $composerLockDiffDto = $this->packageManager->getComposerLockDiff();

        if ($composerLockDiffDto->isEmpty()) {
            return $stepsExecutionDto
                ->setIsSuccessful(false)
                ->addOutputMessage('The branch is up to date. No further action is required.');
        }

        return $stepsExecutionDto->addComposerLockDiff($composerLockDiffDto);
    }
}
