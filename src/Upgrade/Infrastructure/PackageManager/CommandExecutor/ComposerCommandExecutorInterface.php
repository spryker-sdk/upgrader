<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager\CommandExecutor;

use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;

interface ComposerCommandExecutorInterface
{
    public function updateSubPackage(PackageCollection $packageCollection): PackageManagerResponseDto;

    public function require(PackageCollection $packageCollection): PackageManagerResponseDto;

    public function remove(PackageCollection $packageCollection): PackageManagerResponseDto;

    public function requireDev(PackageCollection $packageCollection): PackageManagerResponseDto;

    public function update(): PackageManagerResponseDto;

    public function updateLockHash(): PackageManagerResponseDto;
}
