<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\ComposerClient;

use Upgrader\Business\ComposerClient\Command\UpdateCommand;
use Upgrader\Business\ComposerClient\ComposerFile\ComposerJson\ComposerJsonReader;
use Upgrader\Business\ComposerClient\ComposerFile\ComposerJson\ComposerJsonWriter;
use Upgrader\Business\ComposerClient\ComposerFile\ComposerLock\ComposerLockReader;
use Upgrader\UpgraderConfig;

class ComposerClientFactory
{
    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    /**
     * @param \Upgrader\UpgraderConfig $config
     */
    public function __construct(UpgraderConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return \Upgrader\Business\ComposerClient\Command\UpdateCommand
     */
    public function createUpdateCommand(): UpdateCommand
    {
        return new UpdateCommand($this->config->getCommandExecuteTimeout());
    }

    /**
     * @return \Upgrader\Business\ComposerClient\ComposerFile\ComposerJson\ComposerJsonReader
     */
    public function createComposerJsonReader(): ComposerJsonReader
    {
        return new ComposerJsonReader();
    }

    /**
     * @return \Upgrader\Business\ComposerClient\ComposerFile\ComposerJson\ComposerJsonWriter
     */
    public function createComposerJsonWriter(): ComposerJsonWriter
    {
        return new ComposerJsonWriter();
    }

    /**
     * @return \Upgrader\Business\ComposerClient\ComposerFile\ComposerLock\ComposerLockReader
     */
    public function createComposerLockReader(): ComposerLockReader
    {
        return new ComposerLockReader();
    }
}
