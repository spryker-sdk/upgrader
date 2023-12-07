<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Presentation\Checker\DbSchemaConflictChecker;

use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\Dto\ViolationDto;
use Upgrade\Infrastructure\VersionControlSystem\Generator\CheckerViolationMessageBuilderInterface;

class ViolationBodyMessageBuilder implements CheckerViolationMessageBuilderInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'Custom project table columns conflict with the new columns installed by upgrader.';

    /**
     * @param array<\DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\Dto\ViolationDto> $violations
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     *
     * @return string
     */
    public function buildViolationsMessage(array $violations, array $packageDtos): string
    {
        $header = static::ERROR_MESSAGE;

        $text = 'You need to rename column(s) or table(s) to fix it.';
        $text .= PHP_EOL . PHP_EOL
            . '| Table | Columns | Project file |'
            . PHP_EOL
            . '|------------------|-----------------|-----------------|'
            . PHP_EOL;

        foreach ($violations as $violation) {
            $text .= implode(
                ' | ',
                [
                    $violation->getTable(),
                    implode('<br>', $violation->getColumns()),
                    $violation->getProjectFile(),
                    PHP_EOL,
                ],
            );
        }

        $text .= PHP_EOL;

        return "<details><summary><h4>$header</h4></summary>$text</details>";
    }

    /**
     * @return string
     */
    public function getSupportedType(): string
    {
        return ViolationDto::class;
    }
}
