<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Exception\ReleaseGroupValidatorException;

class ConflictValidator implements ReleaseGroupValidatorInterface
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
        if ($releaseGroup->isConflictDetected()) {
            $message = sprintf(
                'Release group "%s" contains module conflicts. Please follow the link below to find addition information about the conflict %s',
                $releaseGroup->getName(),
                PHP_EOL . $releaseGroup->getLink(),
            );

            throw new ReleaseGroupValidatorException($message);
        }
    }
}
