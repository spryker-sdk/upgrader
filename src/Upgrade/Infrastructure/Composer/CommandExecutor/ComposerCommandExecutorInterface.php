<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Composer\CommandExecutor;

use Upgrade\Domain\Entity\Collection\PackageCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\ExecutionDto;
use Upgrade\Application\Dto\StepsExecutionDto;

interface ComposerCommandExecutorInterface
{
    /**
     * @param PackageCollection $packageCollection
     * @return \Upgrade\Domain\Entity\\Upgrade\Application\Dto\ExecutionDto
     */
    public function require(PackageCollection $packageCollection): ExecutionDto;

    /**
     * @param \Upgrade\Domain\Entity\PackageCollection $packageCollection
     * @return \Upgrade\Domain\Entity\ExecutionDto
     */
    public function requireDev(PackageCollection $packageCollection): ExecutionDto;

    /**
     * @return \Upgrade\Domain\Entity\\Upgrade\Application\Dto\ExecutionDto
     */
    public function update(): ExecutionDto;
}
