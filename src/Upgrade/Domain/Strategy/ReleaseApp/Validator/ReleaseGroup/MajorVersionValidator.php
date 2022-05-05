<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseApp\Infrastructure\Presentation\Entity\ReleaseGroupDto;
use Upgrade\Infrastructure\Exception\UpgraderException;

class MajorVersionValidator implements ReleaseGroupValidatorInterface
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
        $moduleWithMajorUpdate = $releaseGroup->getModuleCollection()->getFirstMajor();
        if ($moduleWithMajorUpdate) {
            $message = sprintf(
                'There is a major release available for module %s. Please follow the link below to find all documentation needed to help you upgrade to the latest release %s',
                $moduleWithMajorUpdate->getName(),
                PHP_EOL . $releaseGroup->getLink(),
            );

            throw new UpgraderException($message);
        }
    }
}
