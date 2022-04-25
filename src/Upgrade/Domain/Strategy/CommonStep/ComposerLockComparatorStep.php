<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\CommonStep;

use Upgrade\Domain\Dto\Step\StepsExecutionDto;
use Upgrade\Domain\Strategy\Comparator\ComposerLockComparator;
use Upgrade\Domain\Strategy\StepInterface;

class ComposerLockComparatorStep implements StepInterface
{
    /**
     * @var \Upgrade\Domain\Strategy\Comparator\ComposerLockComparator
     */
    protected $composerLockComparator;

    /**
     * @param \Upgrade\Domain\Strategy\Comparator\ComposerLockComparator $composerLockComparator
     */
    public function __construct(ComposerLockComparator $composerLockComparator)
    {
        $this->composerLockComparator = $composerLockComparator;
    }

    /**
     * @param \Upgrade\Domain\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $composerLockDiffDto = $this->composerLockComparator->getComposerLockDiff();

        if ($composerLockDiffDto->isEmpty()) {
            return $stepsExecutionDto
                ->setIsSuccessful(false)
                ->addOutputMessage('The branch is up to date. No further action is required.');
        }

        return $stepsExecutionDto->addComposerLockDiff($composerLockDiffDto);
    }
}
