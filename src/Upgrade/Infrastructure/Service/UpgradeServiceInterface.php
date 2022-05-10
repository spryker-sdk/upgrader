<?php

namespace Upgrade\Infrastructure\Service;

namespace Upgrade\Infrastructure\Service;

use Upgrade\Application\Dto\StepsExecutionDto;

interface UpgradeServiceInterface
{
    /**
     * @return StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto;
}
