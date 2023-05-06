<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Adapter;

use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;

interface PackageManagerAdapterInterface
{
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
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function require(PackageCollection $packageCollection): ResponseDto;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function requireDev(PackageCollection $packageCollection): ResponseDto;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function remove(PackageCollection $packageCollection): ResponseDto;

    /**
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function update(): ResponseDto;

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
