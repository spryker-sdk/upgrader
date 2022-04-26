<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;
use Upgrade\Infrastructure\Exception\UpgraderException;

class MajorVersionValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Dto\ReleaseGroupDto $releaseGroup
     *
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(ReleaseGroupDto $releaseGroup): void
    {
        if ($releaseGroup->getModuleCollection()->getMajorAmount()) {
            $message = sprintf(
                '%s %s',
                'Release group contains major changes. Name:',
                $releaseGroup->getName(),
            );

            throw new UpgraderException($message);
        }
    }
}
