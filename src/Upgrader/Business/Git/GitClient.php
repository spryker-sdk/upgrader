<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Git;

class GitClient
{
    /**
     * @var \Upgrader\Business\Git\GitClientFactory
     */
    protected $factory;

    /**
     * @return bool
     */
    public function isUncommittedChangesExist(): bool
    {
        return $this->getFactory()->createUpdateIndexCommand()->isIndexOutdated();
    }

    /**
     * @return \Upgrader\Business\Git\GitClientFactory
     */
    protected function getFactory(): GitClientFactory
    {
        if ($this->factory === null) {
            $this->factory = new GitClientFactory();
        }

        return $this->factory;
    }
}
