<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Composer;

use Upgrade\Application\Bridge\ComposerClientBridgeInterface;
use Upgrade\Application\Dto\ExecutionDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Infrastructure\Composer\CommandExecutor\ComposerCommandExecutorInterface;
use Upgrade\Infrastructure\Composer\Reader\ComposerJsonReaderInterface;
use Upgrade\Infrastructure\Composer\Reader\ComposerLockReaderInterface;

class ComposerClientBridge implements ComposerClientBridgeInterface
{
    /**
     * @var string
     */
    protected const PACKAGES_KEY = 'packages';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var string
     */
    protected const VERSION_KEY = 'version';

    /**
     * @var \Upgrade\Infrastructure\Composer\CommandExecutor\ComposerCommandExecutorInterface
     */
    protected $composerCommandExecutor;

    /**
     * @var \Upgrade\Infrastructure\Composer\Reader\ComposerJsonReaderInterface
     */
    protected $composerJsonReader;

    /**
     * @var \Upgrade\Infrastructure\Composer\Reader\ComposerLockReaderInterface
     */
    protected $composerLockReader;

    /**
     * @param \Upgrade\Infrastructure\Composer\CommandExecutor\ComposerCommandExecutorInterface $composerCallExecutor
     * @param \Upgrade\Infrastructure\Composer\Reader\ComposerJsonReaderInterface $composerJsonReader
     * @param \Upgrade\Infrastructure\Composer\Reader\ComposerLockReaderInterface $composerLockReader
     */
    public function __construct(
        ComposerCommandExecutorInterface $composerCallExecutor,
        ComposerJsonReaderInterface $composerJsonReader,
        ComposerLockReaderInterface $composerLockReader
    ) {
        $this->composerCommandExecutor = $composerCallExecutor;
        $this->composerJsonReader = $composerJsonReader;
        $this->composerLockReader = $composerLockReader;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        $composerJsonContent = $this->composerJsonReader->read();

        return $composerJsonContent[self::NAME_KEY];
    }

    /**
     * @return array
     */
    public function getComposerJsonFile(): array
    {
        return $this->composerJsonReader->read();
    }

    /**
     * @return array
     */
    public function getComposerLockFile(): array
    {
        return $this->composerLockReader->read();
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ExecutionDto
     */
    public function require(PackageCollection $packageCollection): ExecutionDto
    {
        return $this->composerCommandExecutor->require($packageCollection);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ExecutionDto
     */
    public function requireDev(PackageCollection $packageCollection): ExecutionDto
    {
        return $this->composerCommandExecutor->requireDev($packageCollection);
    }

    /**
     * @return \Upgrade\Application\Dto\ExecutionDto
     */
    public function update(): ExecutionDto
    {
        return $this->composerCommandExecutor->update();
    }

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string
    {
        $composerLock = $this->composerLockReader->read();

        foreach ($composerLock[self::PACKAGES_KEY] as $package) {
            if ($package[self::NAME_KEY] == $packageName) {
                return $package[self::VERSION_KEY];
            }
        }

        return null;
    }

    /**
     * @param string $packageName
     *
     * @return bool
     */
    public function isDevPackage(string $packageName): bool
    {
        $composerJson = $this->composerJsonReader->read();

        if (isset($composerJson['require-dev'][$packageName])) {
            return true;
        }

        return false;
    }
}
