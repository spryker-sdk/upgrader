<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\Composer\Step;

use Upgrade\Application\Dto\Step\StepsExecutionDto;
use PackageManager\Application\Service\PackageManagerInterface;
use Upgrade\Infrastructure\Strategy\CommonStep\AbstractStep;
use Upgrade\Infrastructure\Strategy\RollbackStepInterface;
use Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver;

class ComposerUpdateStep extends AbstractStep implements RollbackStepInterface
{
    /**
     * @var \PackageManager\Application\Service\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver $vscAdapterResolver
     * @param \PackageManager\Application\Service\PackageManagerInterface $packageManager
     */
    public function __construct(VersionControlSystemAdapterResolver $vscAdapterResolver, PackageManagerInterface $packageManager)
    {
        parent::__construct($vscAdapterResolver);

        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $updateResponse = $this->packageManager->update();
        $stepsExecutionDto->setIsSuccessful($updateResponse->isSuccess());

        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function rollBack(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->vsc->restore($stepsExecutionDto);
    }
}
