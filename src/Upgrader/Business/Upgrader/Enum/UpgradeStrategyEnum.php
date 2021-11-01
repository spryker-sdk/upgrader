<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Enum;

use Upgrader\Business\Enum\UpgraderEnum;

class UpgradeStrategyEnum extends UpgraderEnum
{
    /**
     * @var string
     */
    public const COMPOSER_UPDATE = 'composer-update';
    /**
     * @var string
     */
    public const RELEASE_GROUP = 'release-group';

    /**
     * @return bool
     */
    public function isComposerUpdate(): bool
    {
        return $this->getValue() === static::COMPOSER_UPDATE;
    }

    /**
     * @return bool
     */
    public function isReleaseGroup(): bool
    {
        return $this->getValue() === static::RELEASE_GROUP;
    }
}
