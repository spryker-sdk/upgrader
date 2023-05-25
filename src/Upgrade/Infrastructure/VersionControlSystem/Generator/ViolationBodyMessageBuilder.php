<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

class ViolationBodyMessageBuilder
{
    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Generator\CheckerViolationMessageBuilderRegistry
     */
    protected CheckerViolationMessageBuilderRegistry $checkerViolationMessageBuilderRegistry;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Generator\CheckerViolationMessageBuilderRegistry $checkerViolationMessageBuilderRegistry
     */
    public function __construct(
        CheckerViolationMessageBuilderRegistry $checkerViolationMessageBuilderRegistry
    ) {
        $this->checkerViolationMessageBuilderRegistry = $checkerViolationMessageBuilderRegistry;
    }

    /**
     * @param array<\Upgrade\Application\Dto\ViolationDtoInterface> $violations
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     *
     * @return string
     */
    public function buildViolationsMessage(array $violations, array $packageDtos): string
    {
        $text = '';
        $groupedViolations = $this->groupViolationsByType($violations);

        foreach ($groupedViolations as $type => $violations) {
            $checkerMessageBuilder = $this->checkerViolationMessageBuilderRegistry->getBuilderByType($type);
            $text .= $checkerMessageBuilder->buildViolationsMessage($violations, $packageDtos);
            $text .= PHP_EOL;
        }

        return $text;
    }

    /**
     * @param array<\Upgrade\Application\Dto\ViolationDtoInterface> $violations
     *
     * @return array<string, array<\Upgrade\Application\Dto\ViolationDtoInterface>>
     */
    protected function groupViolationsByType(array $violations): array
    {
        $groupedViolations = [];

        foreach ($violations as $violation) {
            $violationType = get_class($violation);

            if (!isset($groupedViolations[$violationType])) {
                $groupedViolations[$violationType] = [];
            }

            $groupedViolations[$violationType][] = $violation;
        }

        return $groupedViolations;
    }
}
