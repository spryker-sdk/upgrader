<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Service;

use Upgrade\Application\Dto\StepsResponseDto;

interface UpgradeServiceInterface
{
    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto;
}
