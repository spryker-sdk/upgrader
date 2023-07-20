<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Presentation\Checker\ClassExtendsUpdatedPackageChecker;

use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto;
use Upgrade\Infrastructure\VersionControlSystem\Generator\CheckerViolationMessageBuilderInterface;

/**
 * @codeCoverageIgnore
 *
 * Class latter will be moved into the commit comments
 */
class ViolationBodyMessageBuilder implements CheckerViolationMessageBuilderInterface
{
    /**
     * @param array<\DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto> $violations
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     *
     * @return string
     */
    public function buildViolationsMessage(array $violations, array $packageDtos): string
    {
        $text = '';

        foreach ($this->getGroupedViolationsByMessage($violations) as $message => $groupedViolationsByMessage) {
            $header = ':warning: ' . $message;
            $body = '<br>Switch to this branch, bootstrap your project in the development environment, open the mentioned file, and compare its correctness to the released version by Spryker.';
            $body .= PHP_EOL . PHP_EOL
                . '| Package | Release | Classes that overrides the core private API | '
                . PHP_EOL
                . '|---------|---------|-----------------|'
                . PHP_EOL;

            $body .= $this->getMessageViolationsTableLines($groupedViolationsByMessage, $packageDtos);

            $text = "<details><summary>$header</summary>$body</details>";
            $text .= PHP_EOL;
        }

        return $text;
    }

    /**
     * @param array<\DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto> $groupedViolationsByMessage
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     *
     * @return string
     */
    protected function getMessageViolationsTableLines(array $groupedViolationsByMessage, array $packageDtos): string
    {
        $text = '';

        foreach ($this->getGroupedViolationsByPackage($groupedViolationsByMessage) as $package => $violationsByPackage) {
            $text .= implode(
                ' | ',
                [
                    '**' . $package . '**',
                    sprintf($this->getPackageReleaseLink($packageDtos, $package)),
                    implode(
                        '<br>',
                        array_unique(array_map(static fn (ViolationDto $violations): string => $violations->getTarget(), $violationsByPackage)),
                    ),
                    PHP_EOL,
                ],
            );
        }

        return $text;
    }

    /**
     * @param array<\DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto> $violations
     *
     * @return array<string, array<\DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto>>
     */
    protected function getGroupedViolationsByMessage(array $violations): array
    {
        $groupedViolations = [];

        foreach ($violations as $violation) {
            if (!isset($groupedViolations[$violation->getMessage()])) {
                $groupedViolations[$violation->getMessage()] = [];
            }

            $groupedViolations[$violation->getMessage()][] = $violation;
        }

        return $groupedViolations;
    }

    /**
     * @param array<\DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto> $violations
     *
     * @return array<string, array<\DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto>>
     */
    protected function getGroupedViolationsByPackage(array $violations): array
    {
        $groupedViolations = [];

        foreach ($violations as $violation) {
            if (!isset($groupedViolations[$violation->getPackage()])) {
                $groupedViolations[$violation->getPackage()] = [];
            }

            $groupedViolations[$violation->getPackage()][] = $violation;
        }

        return $groupedViolations;
    }

    /**
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     * @param string $package
     *
     * @return string
     */
    protected function getPackageReleaseLink(array $packageDtos, string $package): string
    {
        foreach ($packageDtos as $packageDto) {
            if ($packageDto->getName() === $package) {
                return sprintf('[%s](https://github.com/%s/releases/tag/%s)', $packageDto->getVersion(), $package, $packageDto->getVersion());
            }
        }

        return '-';
    }

    /**
     * @return string
     */
    public function getSupportedType(): string
    {
        return ViolationDto::class;
    }
}
