<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Processor;

use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;
use PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;

interface ReleaseGroupRequireProcessorInterface
{
    /**
     * @return string
     */
    public function getProcessorName(): string;

    /**
     * @param \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection $requiteRequestCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection
     */
    public function requireCollection(ReleaseGroupDtoCollection $requiteRequestCollection): PackageManagerResponseDtoCollection;
}
