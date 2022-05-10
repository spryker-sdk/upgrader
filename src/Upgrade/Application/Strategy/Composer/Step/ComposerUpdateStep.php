<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\Composer\Step;

use Upgrade\Application\Bridge\ComposerClientBridgeInterface;
use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Application\Strategy\Common\Step\AbstractStep;
use Upgrade\Application\Strategy\RollbackStepInterface;
use Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver;

class ComposerUpdateStep extends AbstractStep implements RollbackStepInterface
{
    /**
     * @var \Upgrade\Application\Bridge\ComposerClientBridgeInterface
     */
    protected ComposerClientBridgeInterface $packageManager;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver $vscAdapterResolver
     * @param \Upgrade\Application\Bridge\ComposerClientBridgeInterface $packageManager
     */
    public function __construct(
        VersionControlSystemAdapterResolver $vscAdapterResolver,
        ComposerClientBridgeInterface $packageManager
    ) {
        parent::__construct($vscAdapterResolver);

        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $updateResponse = $this->packageManager->update();
        $stepsExecutionDto->setIsSuccessful($updateResponse->isSuccessful());

        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function rollBack(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->vsc->restore($stepsExecutionDto);
    }
}
