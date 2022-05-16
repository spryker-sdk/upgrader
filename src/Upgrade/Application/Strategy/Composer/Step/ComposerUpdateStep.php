<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\Composer\Step;

use Upgrade\Application\Bridge\PackageManagerBridgeInterface;
use Upgrade\Application\Bridge\VersionControlSystemBridgeInterface;
use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Application\Strategy\Common\Step\AbstractStep;
use Upgrade\Application\Strategy\RollbackStepInterface;

class ComposerUpdateStep extends AbstractStep implements RollbackStepInterface
{
    /**
     * @var \Upgrade\Application\Bridge\PackageManagerBridgeInterface
     */
    protected PackageManagerBridgeInterface $packageManager;

    /**
     * @param \Upgrade\Application\Bridge\VersionControlSystemBridgeInterface $versionControlSystem
     * @param \Upgrade\Application\Bridge\PackageManagerBridgeInterface $packageManager
     */
    public function __construct(
        VersionControlSystemBridgeInterface $versionControlSystem,
        PackageManagerBridgeInterface $packageManager
    ) {
        parent::__construct($versionControlSystem);

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
        if (!$updateResponse->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage($updateResponse->getOutputMessage());
        }

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
