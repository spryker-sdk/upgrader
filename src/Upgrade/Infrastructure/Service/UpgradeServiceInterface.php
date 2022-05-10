<?php

namespace Upgrade\Infrastructure\Service;

namespace Upgrade\Infrastructure\Service;

use Upgrade\Domain\Dto\Step\StepsExecutionDto;

interface UpgradeServiceInterface
{
    /**
     * @throws \Upgrade\Application\Exception\UpgradeStrategyIsNotDefinedException
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto;
}
