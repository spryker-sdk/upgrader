<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

use Upgrade\Application\Dto\ViolationDto;

/**
 * @codeCoverageIgnore
 *
 * Temp class latter will be moved into the commit comments
 */
class ViolationBodyMessageBuilder
{
    /**
     * @param array<\Upgrade\Application\Dto\ViolationDto> $violations
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     *
     * @return string
     */
    public function buildViolationsTable(array $violations, array $packageDtos): string
    {
        $text = '';

        foreach ($this->getGroupedViolationsByMessage($violations) as $message => $groupedViolationsByMessage) {
            $text .= PHP_EOL . '[WARNING] ' . $message . PHP_EOL;
            $text .= '| Package | Release | Project file | '
                . PHP_EOL
                . '|---------|---------|--------------|'
                . PHP_EOL;

            $text .= $this->getMessageViolationsTableLines($groupedViolationsByMessage, $packageDtos);
            $text .= PHP_EOL;
        }

        return $text;
    }

    /**
     * @param array<\Upgrade\Application\Dto\ViolationDto> $groupedViolationsByMessage
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
     * @param array<\Upgrade\Application\Dto\ViolationDto> $violations
     *
     * @return array<string, array<\Upgrade\Application\Dto\ViolationDto>>
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
     * @param array<\Upgrade\Application\Dto\ViolationDto> $violations
     *
     * @return array<string, array<\Upgrade\Application\Dto\ViolationDto>>
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
}
