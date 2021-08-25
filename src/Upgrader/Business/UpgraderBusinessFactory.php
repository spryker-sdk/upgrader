<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Composer\ComposerClient;
use Upgrader\Business\Git\GitClient;
use Upgrader\UpgraderConfig;

class UpgraderBusinessFactory
{
    /**
     * @var \Upgrader\UpgraderConfig
     */
    private $config;

    public function __construct()
    {
        $this->config = new UpgraderConfig();
    }

    /**
     * @return \Upgrader\Business\Git\GitClient
     */
    public function createGitClient(): GitClient
    {
        return new GitClient();
    }

    /**
     * @return \Upgrader\Business\Composer\ComposerClient
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
        return $this->config;
    }
}
