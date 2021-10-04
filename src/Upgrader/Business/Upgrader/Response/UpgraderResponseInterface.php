<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Response;

interface UpgraderResponseInterface
{
    /**
     * @var int
     */
    public const CODE_ERROR = 1;

    /**
     * @var int
     */
    public const CODE_SUCCESS = 0;

    /**
     * @return int
     */
    public function getExitCode(): int;

    /**
     * @return string|null
     */
    public function getOutput(): ?string;
}
