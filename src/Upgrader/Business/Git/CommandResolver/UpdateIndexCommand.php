<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Git\CommandResolver;

use Upgrader\Business\CommandExecutor\AbstractCommandExecutor;

class UpdateIndexCommand extends AbstractCommandExecutor
{
    public const REFRESH_FLAG = '--refresh';

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'git update-index';
    }

    /**
     * @return bool
     */
    public function isIndexOutdated(): bool
    {
        $commandResultDto = $this->exec($this->getCommand() . self::REFRESH_FLAG);

        return $commandResultDto->getResultCode();
    }
}
