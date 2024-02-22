<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager\CommandExecutor;

use RuntimeException;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReader;

class ComposerCommandExecutor implements ComposerCommandExecutorInterface
{
    /**
     * @var array<string, int>
     */
    public const ENV = ['COMPOSER_PROCESS_TIMEOUT' => 36000];

    /**
     * @var string
     */
    protected const COMPOSER_COMMAND_NAME = 'composer';

    /**
     * @var string
     */
    protected const REQUIRE_COMMAND_NAME = 'require';

    /**
     * @var string
     */
    protected const REMOVE_COMMAND_NAME = 'remove';

    /**
     * @var string
     */
    protected const UPDATE_COMMAND_NAME = 'update';

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
     * @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var bool
     */
    protected bool $isUpdateMinimumDependeciesEnabled;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReader
     */
    protected ComposerLockReader $composerLockReader;

    /**
     * @param \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReader $composerLockReader
     * @param bool $isUpdateMinimumDependeciesEnabled
     */
    public function __construct(
        ProcessRunnerServiceInterface $processRunner,
        ConfigurationProviderInterface $configurationProvider,
        ComposerLockReader $composerLockReader,
        bool $isUpdateMinimumDependeciesEnabled = false
    ) {
        $this->processRunner = $processRunner;
        $this->configurationProvider = $configurationProvider;
        $this->isUpdateMinimumDependeciesEnabled = $isUpdateMinimumDependeciesEnabled;
        $this->composerLockReader = $composerLockReader;
    }

    /**
     * @return array<string>
     */
    protected function getUpdateWithList(): array
    {
        return (!$this->isUpdateMinimumDependeciesEnabled) ?
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
        return $this->runWithDependencyFlags([
            static::COMPOSER_COMMAND_NAME,
            static::UPDATE_COMMAND_NAME,
            ...$this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
            static::NO_INTERACTION_FLAG,
        ]);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function require(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->runWithDependencyFlags([
            static::COMPOSER_COMMAND_NAME,
            static::REQUIRE_COMMAND_NAME,
            ...$this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
        ]);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function remove(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        $command = [
            static::COMPOSER_COMMAND_NAME,
            static::REMOVE_COMMAND_NAME,
            ...$this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
        ];

        return $this->createResponse($this->processRunner->run($command, static::ENV));
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function requireDev(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->runWithDependencyFlags([
            static::COMPOSER_COMMAND_NAME,
            static::REQUIRE_COMMAND_NAME,
            ...$this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
            static::DEV_FLAG,
        ]);
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function update(): PackageManagerResponseDto
    {
        return $this->runWithDependencyFlags([
            static::COMPOSER_COMMAND_NAME,
            static::UPDATE_COMMAND_NAME,
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
            static::NO_INTERACTION_FLAG,
        ]);
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function updateLockHash(): PackageManagerResponseDto
    {
        $package = $this->getFirstAvailablePackage();

        $command = [
            static::COMPOSER_COMMAND_NAME,
            static::UPDATE_COMMAND_NAME,
            sprintf('%s:%s', $package['name'], $package['version']),
            static::NO_PLUGINS_FLAG,
            static::NO_SCRIPTS_FLAG,
            static::NO_INSTALL_FLAG,
            static::NO_INTERACTION_FLAG,
        ];

        return $this->createResponse($this->processRunner->run($command, static::ENV));
    }

    /**
     * @thorws \RuntimeException
     *
     * @throws \RuntimeException
     *
     * @return array<string, string>
     */
    protected function getFirstAvailablePackage(): array
    {
        $composerLock = $this->composerLockReader->read();

        $package = $composerLock['packages-dev'][0] ?? $composerLock['packages'][0] ?? null;

        if ($package === null) {
            throw new RuntimeException('Unable to find package in composer.lock');
        }

        if (!isset($package['name'], $package['version'])) {
            throw new RuntimeException('Unable to find package name or version');
        }

        return $package;
    }

    /**
     * @param array<string> $command
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    protected function runWithDependencyFlags(array $command): PackageManagerResponseDto
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
     * @return array<string>
     */
    protected function getPackageString(PackageCollection $packageCollection): array
    {
        $result = [];
        foreach ($packageCollection->toArray() as $package) {
            if ($package->getVersion() === '') {
                $result[] = $package->getName();

                continue;
            }
            $version = $package->getVersion();
            if (str_contains($package->getVersion(), ' ')) {
                $version = sprintf('%s', $version);
            }
            $package = sprintf('%s:%s', $package->getName(), $version);
            $result[] = $package;
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
