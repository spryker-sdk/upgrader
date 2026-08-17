<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Service;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Service\UpgradeService as ApplicationService;

class UpgradeService implements UpgradeServiceInterface
{
    protected ApplicationService $application;

    public function __construct(ApplicationService $application)
    {
        $this->application = $application;
    }

    public function upgrade(): StepsResponseDto
    {
        return $this->application->upgrade();
    }
}
