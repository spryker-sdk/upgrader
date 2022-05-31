<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\ResponseDto;

interface ThresholdSoftValidatorInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $moduleDtoCollection
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function validate(ReleaseGroupDtoCollection $moduleDtoCollection): ResponseDto;
}
