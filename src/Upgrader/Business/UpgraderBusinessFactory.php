<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\ComposerClient\ComposerClient;
use Upgrader\Business\GitClient\GitClient;
use Upgrader\UpgraderConfig;

class UpgraderBusinessFactory
{
    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    public function __construct()
    {
        $this->config = new UpgraderConfig();
    }

    /**
     * @return \Upgrader\Business\GitClient\GitClient
     */
    public function createGitClient(): GitClient
    {
        return new GitClient($this->config);
    }

    /**
     * @return \Upgrader\Business\ComposerClient\ComposerClient
     */
    public function createComposerClient(): ComposerClient
    {
        return new ComposerClient($this->config);
    }

    /**
     * @return \Upgrader\UpgraderConfig
     */
    public function getConfig(): UpgraderConfig
    {
        if ($this->config === null) {
            $this->config = new UpgraderConfig();
        }

        return $this->config;
    }
}
