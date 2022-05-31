<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Service;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Service\UpgradeService as ApplicationService;

class UpgradeService implements UpgradeServiceInterface
{
    /**
     * @var \Upgrade\Application\Service\UpgradeService
     */
    protected ApplicationService $application;

    /**
     * @param \Upgrade\Application\Service\UpgradeService $application
     */
    public function __construct(ApplicationService $application)
    {
        $this->application = $application;
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto
    {
        return $this->application->upgrade();
    }
}
