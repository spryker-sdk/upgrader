<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer;

use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\PackageManager\Client\PackageManagerClientInterface;

class ComposerClient implements PackageManagerClientInterface
{
    /**
     * @var \Upgrader\Business\Command\CommandInterface
     */
    protected $composerUpdateCommand;

    /**
     * @param \Upgrader\Business\Command\CommandInterface $composerUpdateCommand
     */
    public function __construct(CommandInterface $composerUpdateCommand)
    {
        $this->composerUpdateCommand = $composerUpdateCommand;
    }

    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function runUpdate(): CommandResultOutput
    {
        return $this->composerUpdateCommand->run();
    }
}
