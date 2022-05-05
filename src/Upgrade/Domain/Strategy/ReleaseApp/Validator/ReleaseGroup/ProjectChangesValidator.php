<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseApp\Infrastructure\Presentation\Entity\ReleaseGroupDto;
use Upgrade\Infrastructure\Exception\UpgraderException;

class ProjectChangesValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Presentation\Entity\ReleaseGroupDto $releaseGroup
     *
     * @return void
     *@throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     */
    public function validate(ReleaseGroupDto $releaseGroup): void
    {
        if ($releaseGroup->isContainsProjectChanges()) {
            $message = sprintf(
                'Release group %s contains changes on project level. Please follow the link below to find all documentation needed to help you upgrade to the latest release %s',
                $releaseGroup->getName(),
                PHP_EOL . $releaseGroup->getLink(),
            );

            throw new UpgraderException($message);
        }
    }
}
