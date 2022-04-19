<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Client;

use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto;

interface ComposerClientInterface
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
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDtoDto;

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDtoDto;

    /**
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function update(): PackageManagerResponseDtoDto;

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
}
