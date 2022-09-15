<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Exception\ReleaseGroupValidatorException;

class MajorVersionValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     *
     * @throws \Upgrade\Application\Exception\ReleaseGroupValidatorException
     *
     * @return void
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

            throw new ReleaseGroupValidatorException($message);
        }
    }
}
