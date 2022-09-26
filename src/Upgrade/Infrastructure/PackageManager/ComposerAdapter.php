<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerCommandExecutorInterface;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerLockComparatorCommandExecutorInterface;
use Upgrade\Infrastructure\PackageManager\Reader\ComposerJsonReaderInterface;
use Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReaderInterface;

class ComposerAdapter implements PackageManagerAdapterInterface
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
     * @var \Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerCommandExecutorInterface
     */
    protected ComposerCommandExecutorInterface $composerCommandExecutor;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerLockComparatorCommandExecutorInterface
     */
    protected ComposerLockComparatorCommandExecutorInterface $composerLockComparator;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\Reader\ComposerJsonReaderInterface
     */
    protected ComposerJsonReaderInterface $composerJsonReader;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReaderInterface
     */
    protected ComposerLockReaderInterface $composerLockReader;

    /**
     * @param \Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerCommandExecutorInterface $composerCommandExecutor
     * @param \Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerLockComparatorCommandExecutorInterface $composerLockComparator
     * @param \Upgrade\Infrastructure\PackageManager\Reader\ComposerJsonReaderInterface $composerJsonReader
     * @param \Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReaderInterface $composerLockReader
     */
    public function __construct(
        ComposerCommandExecutorInterface $composerCommandExecutor,
        ComposerLockComparatorCommandExecutorInterface $composerLockComparator,
        ComposerJsonReaderInterface $composerJsonReader,
        ComposerLockReaderInterface $composerLockReader
    ) {
        $this->composerCommandExecutor = $composerCommandExecutor;
        $this->composerLockComparator = $composerLockComparator;
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
     * @return array<mixed>
     */
    public function getComposerJsonFile(): array
    {
        return $this->composerJsonReader->read();
    }

    /**
     * @return array<mixed>
     */
    public function getComposerLockFile(): array
    {
        return $this->composerLockReader->read();
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function require(PackageCollection $packageCollection): ResponseDto
    {
        return $this->composerCommandExecutor->require($packageCollection);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function requireDev(PackageCollection $packageCollection): ResponseDto
    {
        return $this->composerCommandExecutor->requireDev($packageCollection);
    }

    /**
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function update(): ResponseDto
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

    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto
    {
        return $this->composerLockComparator->getComposerLockDiff();
    }
}
