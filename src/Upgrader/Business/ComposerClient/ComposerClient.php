<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\ComposerClient;

use Upgrader\Business\Command\CommandResultInterface;
use Upgrader\UpgraderConfig;

class ComposerClient implements ComposerClientInterface
{
    /**
     * @var \Upgrader\Business\ComposerClient\ComposerClientFactory
     */
    protected $factory;

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
     * @return \Upgrader\Business\Command\CommandResultInterface
     */
    public function runComposerUpdate(): CommandResultInterface
    {
        return $this->getFactory()->createUpdateCommand()->exec();
    }

    /**
     * @return \Upgrader\Business\ComposerClient\ComposerClientFactory
     */
    protected function getFactory(): ComposerClientFactory
    {
        if ($this->factory === null) {
            $this->factory = new ComposerClientFactory($this->config);
        }

        return $this->factory;
    }
}
