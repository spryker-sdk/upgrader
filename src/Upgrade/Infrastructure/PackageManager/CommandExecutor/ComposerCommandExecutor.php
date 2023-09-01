<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager\CommandExecutor;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;

class ComposerCommandExecutor implements ComposerCommandExecutorInterface
{
    /**
     * @var array<string, int>
     */
    public const ENV = ['COMPOSER_PROCESS_TIMEOUT' => 36000];

    /**
     * @var string
     */
    protected const REQUIRE_COMMAND_NAME = 'composer require';

    /**
     * @var string
     */
    protected const REMOVE_COMMAND_NAME = 'composer remove';

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
    protected const NO_PLUGINS_FLAG = '--no-plugins';

    /**
     * @var string
     */
    protected const NO_INTERACTION_FLAG = '--no-interaction';

    /**
     * @var string
     */
    protected const WITH_DEPENDENCIES_FLAG = '-w';

    /**
     * @var string
     */
    protected const WITH_ALL_DEPENDENCIES_FLAG = '-W';

    /**
     * @var string
     */
    protected const DEV_FLAG = '--dev';

    /**
     * @var string
     */
    protected const NO_INSTALL_FLAG = '--no-install';

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var bool
     */
    protected bool $isUpdateWithStrategyEnabled;

    /**
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param bool $isUpdateWithStrategyEnabled
     */
    public function __construct(
        ProcessRunnerServiceInterface $processRunner,
        ConfigurationProviderInterface $configurationProvider,
        bool $isUpdateWithStrategyEnabled = false
    ) {
        $this->processRunner = $processRunner;
        $this->configurationProvider = $configurationProvider;
        $this->isUpdateWithStrategyEnabled = $isUpdateWithStrategyEnabled;
    }

    /**
     * @return array<string>
     */
    protected function getUpdateWithList(): array
    {
        return (!$this->isUpdateWithStrategyEnabled) ?
            [
                static::WITH_ALL_DEPENDENCIES_FLAG,
            ] :
            [
                '',
                static::WITH_DEPENDENCIES_FLAG,
                static::WITH_ALL_DEPENDENCIES_FLAG,
            ];
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function updateSubPackage(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        $command = explode(' ', sprintf(
            '%s%s',
            static::UPDATE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
        ));

        return $this->createResponse($this->runCommand($command));
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function require(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->runWithDependecyFlags(explode(' ', sprintf(
            '%s%s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
        )));
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function remove(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        $command = explode(' ', sprintf(
            '%s%s %s %s',
            static::REMOVE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
        ));

        return $this->createResponse($this->processRunner->run($command, static::ENV));
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function requireDev(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->runWithDependecyFlags(explode(' ', sprintf(
            '%s%s %s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
            static::DEV_FLAG,
        )));
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function update(): PackageManagerResponseDto
    {
        return $this->runWithDependecyFlags(explode(' ', sprintf(
            '%s %s %s %s',
            static::UPDATE_COMMAND_NAME,
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
            static::NO_INTERACTION_FLAG,
        )));
    }

    /**
     * @param array<string> $command
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    protected function runWithDependecyFlags(array $command): PackageManagerResponseDto
    {
        $updateWithList = $this->getUpdateWithList() ?: [''];
        foreach ($updateWithList as $flag) {
            $commandToRun = $command;
            if ($flag) {
                $commandToRun[] = $flag;
            }
            $process = $this->runCommand($commandToRun);
            if ($process->isSuccessful()) {
                break;
            }
        }

        return $this->createResponse($process);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return string
     */
    protected function getPackageString(PackageCollection $packageCollection): string
    {
        $result = '';
        foreach ($packageCollection->toArray() as $package) {
            if ($package->getVersion() === '') {
                $result = sprintf('%s %s', $result, $package->getName());

                continue;
            }
            $package = sprintf('%s:%s', $package->getName(), $package->getVersion());
            $result = sprintf('%s %s', $result, $package);
        }

        return $result;
    }

    /**
     * @param \Symfony\Component\Process\Process<string, string> $process
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    protected function createResponse(Process $process): PackageManagerResponseDto
    {
        $command = str_replace('\'', '', $process->getCommandLine());
        $output = $process->isTerminated() && !$process->isSuccessful() ? $process->getErrorOutput() ?: $process->getOutput() : '';
        $outputs = array_filter([$command, $output]);

        return new PackageManagerResponseDto($process->isSuccessful(), implode(PHP_EOL, $outputs), [$process->getCommandLine()]);
    }

    /**
     * @param array<string> $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function runCommand(array $command): Process
    {
        if ($this->configurationProvider->getComposerNoInstall()) {
            $command[] = static::NO_INSTALL_FLAG;
        }

        return $this->processRunner->run($command, static::ENV);
    }
}
