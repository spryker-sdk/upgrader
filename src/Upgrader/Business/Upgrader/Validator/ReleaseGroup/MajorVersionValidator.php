<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator\ReleaseGroup;

use Upgrader\Business\DataProvider\Entity\ReleaseGroup;
use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface;

class MajorVersionValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @param \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(ReleaseGroup $releaseGroup): void
    {
        if ($releaseGroup->isContainsMajorUpdates()) {
            $message = sprintf(
                '%s %s',
                'Release group contains major changes. Name:',
                $releaseGroup->getName()
            );

            throw new UpgraderException($message);
        }
    }
}
