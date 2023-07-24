<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

use ReleaseApp\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Application\Dto\ReleaseGroupStatDto;
use Upgrade\Application\Dto\StepsResponseDto;

class PullRequestDataGenerator
{
    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Generator\ViolationBodyMessageBuilder
     */
    protected ViolationBodyMessageBuilder $violationBodyMessageBuilder;

    /**
     * @var \ReleaseApp\Infrastructure\Configuration\ConfigurationProvider
     */
    private ConfigurationProvider $configurationProvider;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Generator\ViolationBodyMessageBuilder $violationBodyMessageBuilder
     * @param \ReleaseApp\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ViolationBodyMessageBuilder $violationBodyMessageBuilder, ConfigurationProvider $configurationProvider)
    {
        $this->violationBodyMessageBuilder = $violationBodyMessageBuilder;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     * @param int|null $releaseGroupId
     *
     * @return string
     */
    public function buildBody(
        StepsResponseDto $stepsResponseDto,
        ?int $releaseGroupId = null
    ): string {
        $text = $this->createDescriptionAboutText(
            $releaseGroupId,
            $stepsResponseDto->getLastAppliedReleaseGroup() ? $stepsResponseDto->getLastAppliedReleaseGroup()->getJiraIssueLink() : null,
        )
            . PHP_EOL
            . PHP_EOL
            . '#### Overview'
            . PHP_EOL
            . $this->getReleaseGroupsStatInfo($stepsResponseDto->getReleaseGroupStatDto())
            . PHP_EOL
            . sprintf('Report ID: %s', $stepsResponseDto->getReportId() ?? 'n/a')
            . PHP_EOL
            . PHP_EOL;

        if ($stepsResponseDto->getBlockerInfo()) {
            $text .= '​' . PHP_EOL . '**The process was faced with the blocker:**' . PHP_EOL . PHP_EOL;
            $text .= $stepsResponseDto->getBlockerInfo();
            $text .= str_repeat(PHP_EOL, 2);
        }

        $integratorResponseDto = $stepsResponseDto->getIntegratorResponseDto();
        if ($integratorResponseDto && $integratorResponseDto->getWarnings()) {
            $text .= '<details>' . PHP_EOL . '<summary>';
            $text .= 'Warnings that happened during the manifests applying process';
            $text .= '</summary>' . str_repeat(PHP_EOL, 2);
            $text .= $this->buildSkippedManifestTable($integratorResponseDto->getWarnings());
            $text .= PHP_EOL . '</details>' . str_repeat(PHP_EOL, 2);
        }

        $composerDiffDto = $stepsResponseDto->getComposerLockDiff();
        if (!$composerDiffDto) {
            return $text;
        }

        $violations = $stepsResponseDto->getViolations();
        if (count($violations) > 0) {
            $text .= '**Needs attention**' . PHP_EOL . PHP_EOL;
            $text .= 'Please review the warnings shown below because they might affect your upgrade' . PHP_EOL;
            $text .= $this->violationBodyMessageBuilder->buildViolationsMessage(
                $violations,
                array_merge($composerDiffDto->getRequiredPackages(), $composerDiffDto->getRequiredDevPackages()),
            );
            $text .= PHP_EOL;
        }

        if (count($composerDiffDto->getRequiredPackages()) > 0) {
            $text .= '**Packages upgraded:**' . PHP_EOL;
            $text .= $this->buildPackageDiffTable($composerDiffDto->getRequiredPackages());
            $text .= PHP_EOL;
        }

        if (count($composerDiffDto->getRequiredDevPackages()) > 0) {
            $text .= '**Packages dev upgraded:**' . PHP_EOL;
            $text .= $this->buildPackageDiffTable($composerDiffDto->getRequiredDevPackages());
            $text .= PHP_EOL;
        }

        return $text;
    }

    /**
     * @param int|null $releaseGroupId
     * @param string|null $jiraIssueLink
     *
     * @return string
     */
    protected function createDescriptionAboutText(?int $releaseGroupId, ?string $jiraIssueLink = null): string
    {
        $text = 'Auto created via Upgrader tool';

        if ($releaseGroupId !== null) {
            $releaseGroupLink = sprintf('%s/release-groups/view/%s', $this->configurationProvider->getReleaseAppUrl(), $releaseGroupId);

            $text .= sprintf(' for [%s](%s) release group', $releaseGroupLink, $releaseGroupLink);

            if ($jiraIssueLink !== null) {
                $text .= sprintf('.%sJira ticket [%s](%s)', PHP_EOL, $jiraIssueLink, $jiraIssueLink);
            }
        }

        $text .= '.';

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

        $processedSkippedManifests = [];

        foreach ($skippedManifests as $skippedManifest) {
            $skippedManifestHash = md5($skippedManifest);

            if (in_array($skippedManifestHash, $processedSkippedManifests, true)) {
                continue;
            }

            $processedSkippedManifests[] = $skippedManifestHash;

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

    /**
     * @param \Upgrade\Application\Dto\ReleaseGroupStatDto $releaseGroupStatDto
     *
     * @return string
     */
    protected function getReleaseGroupsStatInfo(ReleaseGroupStatDto $releaseGroupStatDto): string
    {
        $message = sprintf('Amount of applied release groups: %s', $releaseGroupStatDto->getAppliedRGsAmount());
        if ($releaseGroupStatDto->getAppliedSecurityFixesAmount()) {
            $message .= sprintf(' (including %s security fixes)', $releaseGroupStatDto->getAppliedSecurityFixesAmount());
        }

        return $message;
    }
}
