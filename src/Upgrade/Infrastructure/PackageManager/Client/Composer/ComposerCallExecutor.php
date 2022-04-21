<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Client\Composer;

use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;
use Upgrade\Infrastructure\Process\ProcessRunnerInterface;

class ComposerCallExecutor implements ComposerCallExecutorInterface
{
    /**
     * @var string
     */
    protected const REQUIRE_COMMAND_NAME = 'composer require';

    /**
     * @var string
     */
    protected const UPDATE_COMMAND_NAME = 'composer update';

    /**
     * @var string
     */
    protected const NO_SCRIPTS_FLAG = '--no-scripts';

    /**
     * @var string
     */
    protected const WITH_ALL_DEPENDENCIES_FLAG = '--with-all-dependencies';

    /**
     * @var string
     */
    protected const DEV_FLAG = '--dev';

    /**
     * @var \Upgrade\Infrastructure\Process\ProcessRunnerInterface
     */
    protected $processRunner;

    /**
     * @param \Upgrade\Infrastructure\Process\ProcessRunnerInterface $processRunner
     */
    public function __construct(ProcessRunnerInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        $command = sprintf(
            '%s%s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
        );
        $process = $this->processRunner->runProcess(explode(' ', $command));

        return $this->createResponse($process);
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        $command = sprintf(
            '%s%s %s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
            static::DEV_FLAG,
        );
        $process = $this->processRunner->runProcess(explode(' ', $command));

        return $this->createResponse($process);
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function update(): PackageManagerResponseDto
    {
        $command = sprintf(
            '%s %s %s',
            static::UPDATE_COMMAND_NAME,
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
        );
        $process = $this->processRunner->runProcess(explode(' ', $command));

        return $this->createResponse($process);
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return string
     */
    protected function getPackageString(PackageDtoCollection $packageCollection): string
    {
        $result = '';
        foreach ($packageCollection as $package) {
            $package = sprintf('%s:%s', $package->getName(), $package->getVersion());
            $result = sprintf('%s %s', $result, $package);
        }

        return $result;
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function createResponse(Process $process): PackageManagerResponseDto
    {
        $command = str_replace('\'', '', $process->getCommandLine());
        $output = $process->getExitCode() ? $process->getErrorOutput() : '';
        $outputs = array_filter([$command, $output]);

        return new PackageManagerResponseDto($process->isSuccessful(), implode(PHP_EOL, $outputs));
    }
}
