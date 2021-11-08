<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\CallExecutor;

use Symfony\Component\Process\Process;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\UpgraderConfig;

class CallExecutor implements CallExecutorInterface
{
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
     * @param string $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runProcess(string $command): Process
    {
        $process = new Process(explode(' ', $command), (string)getcwd());
        $process->setTimeout($this->config->getCommandExecutionTimeout());
        $process->run();

        return $process;
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function createResponse(Process $process): PackageManagerResponse
    {
        $command = str_replace('\'', '', $process->getCommandLine());
        $output = $process->getExitCode() ? $process->getErrorOutput() : '';
        $outputs = array_filter([$command, $output]);

        return new PackageManagerResponse($process->isSuccessful(), implode(PHP_EOL, $outputs));
    }
}
