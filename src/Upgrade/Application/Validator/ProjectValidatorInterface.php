<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Validator;

interface ProjectValidatorInterface
{
    /**
     * @return array<\Upgrade\Application\Dto\ValidatorViolationDto>
     */
    public function validateProject(): array;
}
