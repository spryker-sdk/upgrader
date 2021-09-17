<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\PackageManager;

use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface;

interface PackageManagerInterface
{
    /**
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function update(): CommandResponse;

    /**
     * @return string
     */
    public function getProjectName(): string;

    /**
     * @return array
     */
    public function getComposerJsonFile(): array;

    /**
     * @return array
     */
    public function getComposerLockFile(): array;

    /**
     * @param \Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface $packageCollection
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function require(PackageCollectionInterface $packageCollection): CommandResponse;

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string;
}
