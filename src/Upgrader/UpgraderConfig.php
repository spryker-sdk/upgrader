<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader;

class UpgraderConfig
{
    protected const COMPOSER_JSON_PATCH = 'composer.json';
    protected const COMPOSER_LOCK_PATCH = 'composer.lock';

    /**
     * @return string
     */
    public function getComposerJsonPath(): string
    {
        return self::COMPOSER_JSON_PATCH;
    }

    /**
     * @return string
     */
    public function getComposerLockPath(): string
    {
        return self::COMPOSER_LOCK_PATCH;
    }
}
