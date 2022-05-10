<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Composer\CommandExecutor;

use Upgrade\Application\Dto\ExecutionDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;

interface ComposerCommandExecutorInterface
{
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
}
