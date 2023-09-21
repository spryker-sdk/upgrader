<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Presentation\Checker\ModuleNameConflictChecker;

use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ProjectModulesNamesFetcher;
use Upgrade\Infrastructure\VersionControlSystem\Generator\CheckerViolationMessageBuilderInterface;

/**
 * @codeCoverageIgnore
 *
 * Class latter will be moved into the commit comments
 */
class ViolationBodyMessageBuilder implements CheckerViolationMessageBuilderInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'You have a project module which conflicts with the new module we are installing.';

    /**
     * @param array<\DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ViolationDto> $violations
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     *
     * @return string
     */
    public function buildViolationsMessage(array $violations, array $packageDtos): string
    {
        $header = static::ERROR_MESSAGE;

        $text = sprintf('To fix this warning, please rename your project module to have a different name in "src/.*/(%s)/", or ensure, that your module is compatible with Spryker’s one.', implode('|', ProjectModulesNamesFetcher::MODULE_LAYERS));
        $text .= PHP_EOL . PHP_EOL
            . '| Composer command | Conflict project module(s) | '
            . PHP_EOL
            . '|------------------|-----------------|'
            . PHP_EOL;

        foreach ($violations as $violation) {
            $text .= '|' . implode(
                ' | ',
                [
                    implode('<br>', $violation->getComposerCommands()),
                    implode('<br>', $violation->getExistingModules()),
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
