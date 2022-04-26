<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Processor;

use PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;

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
