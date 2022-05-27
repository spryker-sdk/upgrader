<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Bridge\IntegratorBridgeInterface;
use Upgrade\Application\Bridge\VersionControlSystemBridgeInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\RollbackStepInterface;

class IntegratorStep extends AbstractStep implements RollbackStepInterface
{
    /**
     * @var \Upgrade\Application\Bridge\IntegratorBridgeInterface
     */
    protected IntegratorBridgeInterface $integratorClient;

    /**
     * @param \Upgrade\Application\Bridge\VersionControlSystemBridgeInterface $versionControlSystem
     * @param \Upgrade\Application\Bridge\IntegratorBridgeInterface $integratorClient
     */
    public function __construct(VersionControlSystemBridgeInterface $versionControlSystem, IntegratorBridgeInterface $integratorClient)
    {
        parent::__construct($versionControlSystem);

        $this->integratorClient = $integratorClient;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->integratorClient->runIntegrator($stepsExecutionDto);
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
