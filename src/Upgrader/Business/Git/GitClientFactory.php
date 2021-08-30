<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Git;

use Upgrader\Business\Git\CommandResolver\UpdateIndexCommand;

class GitClientFactory
{
    /**
     * @var \Upgrader\Business\Git\CommandResolver\UpdateIndexCommand
     */
    private $statusCommand;

    /**
     * @return \Upgrader\Business\Git\CommandResolver\UpdateIndexCommand
     */
    public function createUpdateIndexCommand(): UpdateIndexCommand
    {
        if ($this->statusCommand === null) {
            $this->statusCommand = new UpdateIndexCommand();
        }

        return $this->statusCommand;
    }
}