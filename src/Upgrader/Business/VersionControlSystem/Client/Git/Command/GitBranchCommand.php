<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Client\Git\Command;

use Symfony\Component\Process\Process;
use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\CommandResponse;

class GitBranchCommand implements CommandInterface
{

    /**
     * @var string
     */
    protected $prBranch;

    public function __construct($prBranch)
    {
        $this->prBranch = $prBranch;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return sprintf('git checkout -b %s', $this->prBranch);
    }

    public function getName(): string
    {
        return 'git:branch:create';
    }

    public function getDescription(): string
    {
        return 'The command for creating a new branch';
    }

    /**
     *
     * @return \Upgrader\Business\Command\CommandResponse
     */
    public function runCommand(): CommandResponse
    {
        $process = new Process(explode(' ', $this->getCommand()), (string)getcwd());
        $process->setTimeout(9000);
        $process->run();

        return new CommandResponse($process, $this->getName());
    }
}
