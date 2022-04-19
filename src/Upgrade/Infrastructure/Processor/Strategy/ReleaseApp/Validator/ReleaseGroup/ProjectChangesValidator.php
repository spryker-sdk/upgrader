<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\ReleaseGroup;

use Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto;
use Upgrade\Infrastructure\Exception\UpgraderException;

class ProjectChangesValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto $releaseGroup
     *
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(ReleaseGroupDto $releaseGroup): void
    {
        if ($releaseGroup->isContainsProjectChanges()) {
            $message = sprintf(
                '%s %s',
                'Release group contains changes on project level. Name:',
                $releaseGroup->getName(),
            );

            throw new UpgraderException($message);
        }
    }
}
