<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\GitClient\Command;

use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Exception\UpgraderCommandExecException;

class UpdateIndexCommand extends AbstractCommand
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
        $command = sprintf('%s %s', $this->getCommand(), self::REFRESH_FLAG);

        try {
            $this->exec($command);
        } catch (UpgraderCommandExecException $exception) {
            return true;
        }

        return false;
    }
}
