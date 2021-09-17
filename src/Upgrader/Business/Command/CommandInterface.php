<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\Command;

use Upgrader\Business\Command\Response\CommandResponse;

interface CommandInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return string
     */
    public function getCommand(): string;

    /**
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function run(): CommandResponse;
}
