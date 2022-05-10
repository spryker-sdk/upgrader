<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Exception\UpgraderException;

class MajorVersionValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     *
     * @return void
     *@throws \Upgrade\Application\Exception\UpgraderException
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
