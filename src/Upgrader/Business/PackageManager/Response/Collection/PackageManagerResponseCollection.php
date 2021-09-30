<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Response\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;

class PackageManagerResponseCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return PackageManagerResponse::class;
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
    public function getSuccessfulOutputs(): array
    {
        return array_map(function ($response) {
            if ($response->isSuccess()) {
                return $response->getOutput();
            }
        }, $this->toArray());
    }
}
