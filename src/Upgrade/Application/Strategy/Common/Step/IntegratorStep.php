<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Bridge\IntegratorBridgeInterface;
use Upgrade\Application\Bridge\VersionControlSystemBridgeInterface;
use Upgrade\Application\Dto\StepsExecutionDto;
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
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->integratorClient->runIntegrator($stepsExecutionDto);
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
