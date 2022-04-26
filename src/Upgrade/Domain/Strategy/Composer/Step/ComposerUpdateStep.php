<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\Composer\Step;

use Upgrade\Domain\Adapter\PackageManagerAdapterInterface;
use Upgrade\Domain\Dto\Step\StepsExecutionDto;
use Upgrade\Domain\Strategy\Common\Step\AbstractStep;
use Upgrade\Domain\Strategy\RollbackStepInterface;
use Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver;

class ComposerUpdateStep extends AbstractStep implements RollbackStepInterface
{
    /**
     * @var \Upgrade\Domain\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver $vscAdapterResolver
     * @param \Upgrade\Domain\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(
        VersionControlSystemAdapterResolver $vscAdapterResolver,
        PackageManagerAdapterInterface $packageManager
    ) {
        parent::__construct($vscAdapterResolver);

        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Domain\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $updateResponse = $this->packageManager->update();
        $stepsExecutionDto->setIsSuccessful($updateResponse->isSuccess());

        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Domain\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function rollBack(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->vsc->restore($stepsExecutionDto);
    }
}
