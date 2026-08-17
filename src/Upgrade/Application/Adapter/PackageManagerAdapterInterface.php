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

    public function getProjectName(): string;

    /**
     * @return array<mixed>
     */
    public function getComposerJsonFile(): array;

    /**
     * @return array<mixed>
     */
    public function getComposerLockFile(): array;

    public function updateSubPackage(PackageCollection $packageCollection): PackageManagerResponseDto;

    public function require(PackageCollection $packageCollection): PackageManagerResponseDto;

    public function requireDev(PackageCollection $packageCollection): PackageManagerResponseDto;

    public function remove(PackageCollection $packageCollection): PackageManagerResponseDto;

    public function update(): PackageManagerResponseDto;

    public function updateLockHash(): PackageManagerResponseDto;

    public function getPackageVersion(string $packageName): ?string;

    public function getPackageConstraint(string $packageName): ?string;

    public function isLockDevPackage(string $packageName): bool;

    public function isDevPackage(string $packageName): bool;

    public function isSubPackage(string $packageName): bool;

    public function getComposerLockDiff(): ComposerLockDiffDto;
}
