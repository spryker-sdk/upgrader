<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader;

use Symfony\Component\Console\Application;
use Upgrader\Communication\Command\UpgradeCommand;

class UpgraderBootstrap extends Application
{
    /**
     * @param string $name
     * @param string $version
     */
    public function __construct($name = 'Upgrader', $version = '1')
    {
        parent::__construct($name, $version);

        $this->setCatchExceptions(false);
    }

    /**
     * @return \Symfony\Component\Console\Command\Command[]
     */
    protected function getDefaultCommands(): array
    {
        $commands = parent::getDefaultCommands();

        foreach ($this->getCommands() as $command) {
            $commands[$command->getName()] = $command;
        }

        return $commands;
    }

    /**
     * @return \Symfony\Component\Console\Command\Command[]
     */
    private function getCommands(): array
    {
        return [
            new UpgradeCommand(),
        ];
    }
}
