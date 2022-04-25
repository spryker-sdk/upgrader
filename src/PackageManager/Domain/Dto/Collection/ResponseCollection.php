<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Dto\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use PackageManager\Domain\Dto\ResponseDto;

class ResponseCollection extends UpgraderCollection
{
    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->isSuccess() ? ResponseDto::CODE_SUCCESS : ResponseDto::CODE_ERROR;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        foreach ($this as $result) {
            if (!$result->isSuccess()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return self::class;
    }
}
