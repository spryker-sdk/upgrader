<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Sender;

use Upgrade\Infrastructure\Report\Dto\ReportDto;

interface ReportSenderInterface
{
    /**
     * @param \Upgrade\Infrastructure\Report\Dto\ReportDto $reportDto
     *
     * @return void
     */
    public function send(ReportDto $reportDto): void;
}
