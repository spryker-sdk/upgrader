<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Response\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\Upgrader\Response\UpgraderResponseInterface;

class UpgraderResponseCollection extends UpgraderCollection
{
    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->isSuccess() ? UpgraderResponseInterface::CODE_SUCCESS : UpgraderResponseInterface::CODE_ERROR;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        foreach ($this->toArray() as $result) {
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
        return UpgraderResponseInterface::class;
    }

    /**
     * @return bool
     */
    public function hasSuccessfulResponse(): bool
    {
        foreach ($this->toArray() as $response) {
            if ($response->isSuccess()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getSuccessfulResults(): array
    {
        $releaseGroups = array_map(function ($response) {
            if ($response->isSuccess()) {
                return implode(' ', $response->getPackageList());
            }
        }, $this->toArray());

        return array_filter($releaseGroups);
    }
}
