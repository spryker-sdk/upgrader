<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\GitClient;

use Upgrader\Business\GitClient\Command\UpdateIndexCommand;
use Upgrader\UpgraderConfig;

class GitClientFactory
{
    /**
     * @var \Upgrader\Business\GitClient\Command\UpdateIndexCommand
     */
    protected $statusCommand;

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
     * @return \Upgrader\Business\GitClient\Command\UpdateIndexCommand
     */
    public function createUpdateIndexCommand(): UpdateIndexCommand
    {
        if ($this->statusCommand === null) {
            $this->statusCommand = new UpdateIndexCommand($this->config->getCommandExecuteTimeout());
        }

        return $this->statusCommand;
    }
}
