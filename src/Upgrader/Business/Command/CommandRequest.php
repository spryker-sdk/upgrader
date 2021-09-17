<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
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
