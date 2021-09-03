<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Client\Git\Command;

use Upgrader\Business\Command\AbstractCommand;

class GitUpdateIndexCommand extends AbstractCommand
{
    protected const COMMAND_NAME = 'git update-index';
    protected const COMMAND_REFRESH_FLAG = '--refresh';

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return sprintf('%s %s', static::COMMAND_NAME, self::COMMAND_REFRESH_FLAG);
    }

//    /**
//     * @return bool
//     */
//    public function isIndexOutdated(): bool
//    {
//        $command = sprintf('%s %s', $this->getCommand(), self::COMMAND_REFRESH_FLAG);
//
//        try {
//            $this->exec($command);
//        } catch (UpgraderCommandExecException $exception) {
//            return true;
//        }
//
//        return false;
//    }
}
