<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface;

interface PackageManagerInterface
{
    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function update(): CommandResultOutput;

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
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function require(PackageCollectionInterface $packageCollection): CommandResultOutput;

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string;
}
