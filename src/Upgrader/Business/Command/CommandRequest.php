<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

class CommandRequest
{
    /**
     * @var string|null
     */
    protected $commandFilterList;

    /**
     * @param string|null $commandFilterList
     */
    public function __construct(?string $commandFilterList = null)
    {
        $this->commandFilterList = $commandFilterList;
    }

    /**
     * @return array
     */
    public function getCommandFilterListAsArray(): array
    {
        if ($this->commandFilterList === null) {
            return [];
        }

        return explode(',', $this->commandFilterList);
    }
}
