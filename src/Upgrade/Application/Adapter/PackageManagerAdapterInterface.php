<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Adapter;

use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;

interface PackageManagerAdapterInterface
{
    /**
     * @var string
     */
    public const COMMAND_META_DATA_KEY = 'command';

    /**
     * @return string
     */
    public function getProjectName(): string;

    /**
     * @return array<mixed>
     */
    public function getComposerJsonFile(): array;

    /**
     * @return array<mixed>
     */
    public function getComposerLockFile(): array;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function updateSubPackage(PackageCollection $packageCollection): PackageManagerResponseDto;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function require(PackageCollection $packageCollection): PackageManagerResponseDto;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function requireDev(PackageCollection $packageCollection): PackageManagerResponseDto;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function remove(PackageCollection $packageCollection): PackageManagerResponseDto;

    /**
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function update(): PackageManagerResponseDto;

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string;

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageConstraint(string $packageName): ?string;

    /**
     * @param string $packageName
     *
     * @return bool
     */
    public function isLockDevPackage(string $packageName): bool;

    /**
     * @param string $packageName
     *
     * @return bool
     */
    public function isDevPackage(string $packageName): bool;

    /**
     * @param string $packageName
     *
     * @return bool
     */
    public function isSubPackage(string $packageName): bool;

    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto;
}
