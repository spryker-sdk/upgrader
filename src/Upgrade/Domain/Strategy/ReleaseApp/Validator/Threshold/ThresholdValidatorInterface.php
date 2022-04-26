<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;

interface ThresholdValidatorInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection $releaseReleaseGroup
     *
     * @return void
     */
    public function validate(ReleaseGroupDtoCollection $releaseReleaseGroup): void;
}
