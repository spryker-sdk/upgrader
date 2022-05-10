<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Bridge;

use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\ExecutionDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;

interface PackageManagerBridgeInterface
{
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
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ExecutionDto
     */
    public function require(PackageCollection $packageCollection): ExecutionDto;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ExecutionDto
     */
    public function requireDev(PackageCollection $packageCollection): ExecutionDto;

    /**
     * @return \Upgrade\Application\Dto\ExecutionDto
     */
    public function update(): ExecutionDto;

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string;

    /**
     * @param string $packageName
     *
     * @return bool
     */
    public function isDevPackage(string $packageName): bool;

    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto;
}
