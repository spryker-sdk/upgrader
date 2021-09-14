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

class GitPushCommand implements CommandInterface
{
    /**
     * @var string
     */
    protected $branch;

    public function __construct($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return sprintf('git push --set-upstream origin %s', $this->branch);
    }

    public function getName(): string
    {
        return 'git:push';
    }

    public function getDescription(): string
    {
        return 'The command for pushing the changes';
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
