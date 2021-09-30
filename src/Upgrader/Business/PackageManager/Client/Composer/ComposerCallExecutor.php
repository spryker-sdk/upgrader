<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer;

use Symfony\Component\Process\Process;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;
use Upgrader\UpgraderConfig;

class ComposerCallExecutor implements ComposerCallExecutorInterface
{
    protected const REQUIRE_COMMAND_NAME = 'composer require';
    protected const NO_SCRIPTS_FLAG = '--no-scripts';

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
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function require(PackageTransferCollection $packageCollection): PackageManagerResponse
    {
        $command = sprintf(
            '%s%s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG
        );
        $process = $this->runProcess($command);

        return $this->createResponse($process);
    }

    /***
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return string
     */
    protected function getPackageString(PackageTransferCollection $packageCollection): string
    {
        $result = '';
        foreach ($packageCollection as $package) {
            $package = sprintf('%s:%s', $package->getName(), $package->getVersion());
            $result = sprintf('%s %s', $result, $package);
        }

        return $result;
    }

    /**
     * @param string $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function runProcess(string $command): Process
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
    protected function createResponse(Process $process): PackageManagerResponse
    {
        $command = str_replace('\'', '', $process->getCommandLine());
        $output = $process->getExitCode() ? $process->getErrorOutput() : '';
        $outputs = array_filter([$command, $output]);

        return new PackageManagerResponse($process->isSuccessful(), implode(PHP_EOL, $outputs));
    }
}
