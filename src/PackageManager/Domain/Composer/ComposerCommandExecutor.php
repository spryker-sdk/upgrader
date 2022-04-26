<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Composer;

use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use PackageManager\Domain\ProcessRunner\ProcessRunnerInterface;
use Symfony\Component\Process\Process;

class ComposerCommandExecutor implements ComposerCommandExecutorInterface
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
     * @var \PackageManager\Domain\ProcessRunner\ProcessRunnerInterface
     */
    protected ProcessRunnerInterface $processRunner;

    /**
     * @param \PackageManager\Domain\ProcessRunner\ProcessRunnerInterface $processRunner
     */
    public function __construct(ProcessRunnerInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
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

        $process = $this->processRunner->runCommand(explode(' ', $command));

        return $this->createResponse($process);
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
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

        $process = $this->processRunner->runCommand(explode(' ', $command));

        return $this->createResponse($process);
    }

    /**
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function update(): PackageManagerResponseDto
    {
        $command = sprintf(
            '%s %s %s',
            static::UPDATE_COMMAND_NAME,
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
        );

        $process = $this->processRunner->runCommand(explode(' ', $command));

        return $this->createResponse($process);
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return string
     */
    protected function getPackageString(PackageDtoCollection $packageCollection): string
    {
        $result = '';
        foreach ($packageCollection->toArray() as $package) {
            $package = sprintf('%s:%s', $package->getName(), $package->getVersion());
            $result = sprintf('%s %s', $result, $package);
        }

        return $result;
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    protected function createResponse(Process $process): PackageManagerResponseDto
    {
        $command = str_replace('\'', '', $process->getCommandLine());
        $output = $process->getExitCode() ? $process->getErrorOutput() : '';
        $outputs = array_filter([$command, $output]);

        return new PackageManagerResponseDto($process->isSuccessful(), implode(PHP_EOL, $outputs));
    }
}
