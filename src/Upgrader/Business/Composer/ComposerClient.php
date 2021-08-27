<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer;

use Upgrader\Business\CommandExecutor\CommandResultDto;
use Upgrader\Business\Composer\CommandExecutor\UpdateCommand;
use Upgrader\Business\Composer\ComposerJson\ComposerJsonReader;
use Upgrader\Business\Composer\ComposerLock\ComposerLockReader;
use Upgrader\UpgraderConfig;

class ComposerClient
{
    /**
     * @var \Upgrader\Business\Composer\ComposerClientFactory
     */
    protected $factory;

    /**
     * @return array
     */
    public function getComposerJsonBodyAsArray(): array
    {
        return $this->getFactory()->createComposerJsonReader()->read();
    }

    /**
     * @return array
     */
    public function getComposerLockBodyAsArray(): array
    {
        return $this->getFactory()->createComposerLockReader()->read();
    }

    /**
     * @return \Upgrader\Business\CommandExecutor\CommandResultDto
     */
    public function runComposerUpdate(): CommandResultDto
    {
        return $this->getFactory()->createUpdateCommand()->execSuccess();
    }

    /**
     * @return \Upgrader\Business\Composer\ComposerClientFactory
     */
    protected function getFactory(): ComposerClientFactory
    {
        if ($this->factory === null) {
            $this->factory = new ComposerClientFactory();
        }

        return $this->factory;
    }
}
