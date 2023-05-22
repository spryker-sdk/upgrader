<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

use InvalidArgumentException;

class CheckerViolationMessageBuilderRegistry
{
    /**
     * @var iterable<\Upgrade\Infrastructure\VersionControlSystem\Generator\CheckerViolationMessageBuilderInterface>
     */
    protected iterable $checkerViolationMessageBuilders;

    /**
     * @param iterable<\Upgrade\Infrastructure\VersionControlSystem\Generator\CheckerViolationMessageBuilderInterface> $checkerViolationMessageBuilders
     */
    public function __construct(iterable $checkerViolationMessageBuilders)
    {
        $this->checkerViolationMessageBuilders = $checkerViolationMessageBuilders;
    }

    /**
     * @param string $type
     *
     * @throws \InvalidArgumentException
     *
     * @return \Upgrade\Infrastructure\VersionControlSystem\Generator\CheckerViolationMessageBuilderInterface
     */
    public function getBuilderByType(string $type): CheckerViolationMessageBuilderInterface
    {
        foreach ($this->checkerViolationMessageBuilders as $checkerViolationMessageBuilder) {
            if ($checkerViolationMessageBuilder->getSupportedType() !== $type) {
                continue;
            }

            return $checkerViolationMessageBuilder;
        }

        throw new InvalidArgumentException(sprintf('Invalid type "%s"', $type));
    }
}
