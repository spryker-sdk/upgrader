<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManagerResponseDto;

interface UpgradeFixerInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     * @param \Upgrade\Application\Dto\PackageManagerResponseDto $packageManagerResponseDto
     *
     * @return bool
     */
    public function isApplicable(ReleaseGroupDto $releaseGroup, PackageManagerResponseDto $packageManagerResponseDto): bool;

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     * @param \Upgrade\Application\Dto\PackageManagerResponseDto $packageManagerResponseDto
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto|null
     */
    public function run(ReleaseGroupDto $releaseGroup, PackageManagerResponseDto $packageManagerResponseDto): ?PackageManagerResponseDto;

    /**
     * @return bool
     */
    public function isReRunStep(): bool;
}
