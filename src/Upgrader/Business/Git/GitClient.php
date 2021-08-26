<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Git;

use Upgrader\Business\Git\CommandResolver\UpdateIndexCommand;

class GitClient
{
    /**
     * @var \Upgrader\Business\Git\CommandResolver\UpdateIndexCommand
     */
    private $statusCommand;

    public function __construct()
    {
        $this->statusCommand = new UpdateIndexCommand();
    }

    /**
     * @return bool
     */
    public function isUncommittedChangesExist(): bool
    {
        return $this->statusCommand->isIndexOutdated();
    }
}
