<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\CommandExecutor;

use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;

interface ComposerCommandExecutorInterface
{
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
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function update(): ResponseDto;
}
