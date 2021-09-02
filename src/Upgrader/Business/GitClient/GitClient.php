<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\GitClient;

use Upgrader\UpgraderConfig;

class GitClient implements GitClientInterface
{
    /**
     * @var \Upgrader\Business\GitClient\GitClientFactory
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
     * @return bool
     */
    public function isUncommittedChangesExist(): bool
    {
        return $this->getFactory()->createUpdateIndexCommand()->isIndexOutdated();
    }

    /**
     * @return \Upgrader\Business\GitClient\GitClientFactory
     */
    protected function getFactory(): GitClientFactory
    {
        if ($this->factory === null) {
            $this->factory = new GitClientFactory($this->config);
        }

        return $this->factory;
    }
}
