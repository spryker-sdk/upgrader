<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Factory;

use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;

class ComposerViolationDtoFactory
{
    /**
     * @var string
     */
    public const VIOLATION_TITLE = 'Composer issues';

    /**
     * @param \Upgrade\Application\Dto\PackageManagerResponseDto $packageManagerResponseDto
     *
     * @return \Upgrade\Application\Dto\ValidatorViolationDto
     */
    public function createFromPackageManagerResponse(PackageManagerResponseDto $packageManagerResponseDto): ValidatorViolationDto
    {
        $errorMessage = $packageManagerResponseDto->getOutputMessage() ?? 'Module fetcher error';
        preg_match('/(?<error>Problem 1.*)/s', $errorMessage, $matches);

        return new ValidatorViolationDto(static::VIOLATION_TITLE, $matches['error'] ?? $errorMessage);
    }
}
