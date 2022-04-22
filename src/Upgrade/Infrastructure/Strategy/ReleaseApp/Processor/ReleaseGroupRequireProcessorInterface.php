<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Processor;

use Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;

interface ReleaseGroupRequireProcessorInterface
{
    /**
     * @return string
     */
    public function getProcessorName(): string;

    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection $requiteRequestCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection
     */
    public function requireCollection(ReleaseGroupDtoCollection $requiteRequestCollection): PackageManagerResponseDtoCollection;
}
