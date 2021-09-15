<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command\ResultOutput\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;

class CommandResultOutputCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return CommandResultOutput::class;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        /** @var \Upgrader\Business\Command\ResultOutput\CommandResultOutput $result */
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
    public function getMessage(): string
    {
        $messageList = [];
        /** @var \Upgrader\Business\Command\ResultOutput\CommandResultOutput $result */
        foreach ($this->toArray() as $result) {
            $messageList[] = $result->getMessage();
        }

        return implode(PHP_EOL, $messageList);
    }
}
