<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\IntegratorResponseDto;

class PullRequestDataGenerator
{
    /**
     * @param \Upgrade\Application\Dto\ComposerLockDiffDto $composerDiffDto
     * @param \Upgrade\Application\Dto\IntegratorResponseDto|null $integratorResponseDto
     * @param string $majorAvailableInfo
     * @param string|null $reportId
     *
     * @return string
     */
    public function buildBody(
        ComposerLockDiffDto $composerDiffDto,
        ?IntegratorResponseDto $integratorResponseDto,
        string $majorAvailableInfo = '',
        ?string $reportId = null
    ): string {
        $text = 'Auto created via Upgrader tool.'
            . PHP_EOL
            . PHP_EOL
            . '#### Overview'
            . PHP_EOL
            . sprintf('Report ID: %s', $reportId ?? 'n/a')
            . PHP_EOL
            . PHP_EOL;

        if ($majorAvailableInfo) {
            $text .= '​' . PHP_EOL . '**Available majors:**' . PHP_EOL . PHP_EOL;
            $text .= $majorAvailableInfo;
        }

        if ($integratorResponseDto && $integratorResponseDto->getWarnings()) {
            $text .= '<details>' . PHP_EOL . '<summary>';
            $text .= 'Warnings that happened during the manifests applying process';
            $text .= '</summary>' . str_repeat(PHP_EOL, 2);
            $text .= $this->buildSkippedManifestTable($integratorResponseDto->getWarnings());
            $text .= PHP_EOL . '</details>' . str_repeat(PHP_EOL, 2);
        }

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

    /**
     * @param array<string> $skippedManifests
     *
     * @return string
     */
    protected function buildSkippedManifestTable(array $skippedManifests): string
    {
        $text = '| Package | Version | Message | '
            . PHP_EOL
            . '|---------|------|--------|'
            . PHP_EOL;

        foreach ($skippedManifests as $skippedManifest) {
            preg_match('/[a-zA-Z]*:[0-9]*.[0-9]*.[0-9]*/', $skippedManifest, $matches);
            if (!count($matches)) {
                continue;
            }
            [$moduleName, $version] = explode(':', reset($matches));

            $row = implode(' | ', [
                '',
                '**' . $moduleName . '**',
                $version,
                $skippedManifest,
                PHP_EOL,
            ]);
            $text .= $row;
        }

        return $text;
    }
}
