<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\CommonStep;

use Upgrade\Application\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Strategy\StepInterface;
use Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver;

class CheckoutStep implements StepInterface
{
    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Adapter\VersionControlSystemAdapterInterface
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
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->vsc->checkout($stepsExecutionDto);
    }
}
