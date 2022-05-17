<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

use Upgrade\Application\Dto\ComposerLockDiffDto;

class PullRequestDataGenerator
{
    /**
     * @param \Upgrade\Application\Dto\ComposerLockDiffDto $composerDiffDto
     * @param string|null $majorAvailableInfo
     *
     * @return string
     */
    public function buildBody(ComposerLockDiffDto $composerDiffDto, ?string $majorAvailableInfo = null): string
    {
        $text = 'Auto created via Upgrader tool.'
            . PHP_EOL
            . PHP_EOL
            . '#### Overview'
            . PHP_EOL;

        if (count($composerDiffDto->getRequireChanges()) > 0) {
            $text .= '**Packages upgraded:**' . PHP_EOL;
            $text .= $this->buildPackageDiffTable($composerDiffDto->getRequireChanges());
            $text .= PHP_EOL;
        }

        if (count($composerDiffDto->getRequireDevChanges()) > 0) {
            $text .= '**Packages dev upgraded:**' . PHP_EOL;
            $text .= $this->buildPackageDiffTable($composerDiffDto->getRequireDevChanges());
            $text .= PHP_EOL;
        }

        if ($majorAvailableInfo) {
            $text .= '​' . PHP_EOL . '**Available majors:**' . PHP_EOL . PHP_EOL;
            $text .= $majorAvailableInfo;
        }

        return $text;
    }

    /**
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     *
     * @return string
     */
    protected function buildPackageDiffTable(array $packageDtos): string
    {
        $text = '| Package | From | To | Changes | '
            . PHP_EOL
            . '|---------|------|----|--------|'
            . PHP_EOL;

        foreach ($packageDtos as $packageDto) {
            $row = implode(' | ', [
                '',
                '**' . $packageDto->getName() . '**',
                $packageDto->getPreviousVersion(),
                $packageDto->getVersion(),
                $packageDto->getDiffLink(),
                PHP_EOL,
            ]);
            $text .= $row;
        }

        return $text;
    }
}
