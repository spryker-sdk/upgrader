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
     * @var \Upgrader\UpgraderConfig
     */
    private $config;

    /**
     * @var \Upgrader\Business\Composer\CommandExecutor\UpdateCommand
     */
    private $updateCommand;

    /**
     * @var \Upgrader\Business\Composer\ComposerJson\ComposerJsonReader
     */
    private $composerJsonReader;

    /**
     * @var \Upgrader\Business\Composer\ComposerLock\ComposerLockReader
     */
    private $composerLockReader;

    /**
     * @param \Upgrader\UpgraderConfig $config
     */
    public function __construct(UpgraderConfig $config)
    {
        $this->config = $config;
        $this->updateCommand = new UpdateCommand();
        $this->composerJsonReader = new ComposerJsonReader($this->config->getComposerJsonPath());
        $this->composerLockReader = new ComposerLockReader($this->config->getComposerLockPath());
    }

    /**
     * @return array
     */
    public function getComposerJsonBodyAsArray(): array
    {
        return $this->composerJsonReader->read();
    }

    /**
     * @return array
     */
    public function getComposerLockBodyAsArray(): array
    {
        return $this->composerLockReader->read();
    }

    /**
     * @return \Upgrader\Business\CommandExecutor\CommandResultDto
     */
    public function runComposerUpdate(): CommandResultDto
    {
        return $this->updateCommand->execSuccess();
    }
}
