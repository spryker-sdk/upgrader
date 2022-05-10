<?php

namespace Upgrade\Infrastructure\Service;

use Upgrade\Application\Service\UpgradeService as ApplicationService;
use Upgrade\Domain\Dto\Step\StepsExecutionDto;

class UpgradeService implements UpgradeServiceInterface
{
    /**
     * @var ApplicationService
     */
    protected ApplicationService $application;

    /**
     * @param ApplicationService $application
     */
    public function __construct(ApplicationService $application)
    {
        $this->application = $application;
    }

    /**
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto
    {
        return $this->application->upgrade();
    }
}
