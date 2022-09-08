<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Composer\Step;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\Step\AbstractStep;
use Upgrade\Application\Strategy\RollbackStepInterface;

class ComposerUpdateStep extends AbstractStep implements RollbackStepInterface
{
    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $versionControlSystem
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(
        VersionControlSystemAdapterInterface $versionControlSystem,
        PackageManagerAdapterInterface $packageManager
    ) {
        parent::__construct($versionControlSystem);

        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $updateResponse = $this->packageManager->update();
        $stepsExecutionDto->setIsSuccessful($updateResponse->isSuccessful());
        if (!$updateResponse->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage($updateResponse->getOutputMessage());
        }

        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function rollBack(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->vsc->restore($stepsExecutionDto);
    }
}
