<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

interface CheckerViolationMessageBuilderInterface
{
    /**
     * @param array<\Upgrade\Application\Dto\ViolationDtoInterface> $violations
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     *
     * @return string
     */
    public function buildViolationsMessage(array $violations, array $packageDtos): string;

    /**
     * @return string
     */
    public function getSupportedType(): string;
}
