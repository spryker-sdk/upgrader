<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator\ReleaseGroup;

use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;

class ProjectChangesValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer $releaseGroup
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(ReleaseGroupTransfer $releaseGroup): void
    {
        if ($releaseGroup->isContainsProjectChanges()) {
            $message = sprintf(
                '%s %s',
                'Release group contains changes on project level. Name:',
                $releaseGroup->getName()
            );

            throw new UpgraderException($message);
        }
    }
}
