<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Client\Git;

use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\VersionControlSystem\Client\VersionControlSystemClientInterface;

class GitClient implements VersionControlSystemClientInterface
{
    protected const ERROR_MESSAGE = 'Please commit or revert your changes.';

    /**
     * @var \Upgrader\Business\Command\CommandInterface
     */
    protected $gitUpdateIndexCommand;

    /**
     * @param \Upgrader\Business\Command\CommandInterface $gitUpdateIndexCommand
     */
    public function __construct(CommandInterface $gitUpdateIndexCommand)
    {
        $this->gitUpdateIndexCommand = $gitUpdateIndexCommand;
    }

    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function isUncommittedChangesExist(): CommandResultOutput
    {
        $commandOutputResult = $this->gitUpdateIndexCommand->run();

        if (!$commandOutputResult->isSuccess()) {
            return new CommandResultOutput(
                $commandOutputResult->getStatusCode(),
                static::ERROR_MESSAGE
            );
        }

        return $commandOutputResult;
    }
}
