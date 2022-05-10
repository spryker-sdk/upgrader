<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Application\Strategy\StepInterface;
use Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver;

class CheckoutStep implements StepInterface
{
    /**
     * @var \Upgrade\Application\Bridge\VersionControlSystemBridgeInterface
     */
    protected $vsc;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver $vscAdapterResolver
     */
    public function __construct(VersionControlSystemAdapterResolver $vscAdapterResolver)
    {
        $this->vsc = $vscAdapterResolver->resolve();
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function run(\Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto): \Upgrade\Application\Dto\StepsExecutionDto
    {
        return $this->vsc->checkout($stepsExecutionDto);
    }
}
