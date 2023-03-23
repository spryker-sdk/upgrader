<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\StepInterface;

class PackagePostUpdateStep implements StepInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\Common\PackagePostUpdateHandler\HandlerInterface>
     */
    protected array $handlerList = [];

    /**
     * @param array<\Upgrade\Application\Strategy\Common\PackagePostUpdateHandler\HandlerInterface> $handlerList
     */
    public function __construct(array $handlerList = [])
    {
        $this->handlerList = $handlerList;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        foreach ($this->handlerList as $handler) {
            if ($handler->isApplicable($stepsExecutionDto)) {
                $stepsExecutionDto = $handler->handle($stepsExecutionDto);
            }
        }

        return $stepsExecutionDto;
    }
}
