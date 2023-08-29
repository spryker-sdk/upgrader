<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\IntegratorResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\Common\IntegratorExecutionValidatorInterface;

/**
 * @codeCoverageIgnore don't need cover presentation view class
 */
class PullRequestDataGenerator
{
    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Generator\ViolationBodyMessageBuilder
     */
    protected ViolationBodyMessageBuilder $violationBodyMessageBuilder;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var \Upgrade\Application\Strategy\Common\IntegratorExecutionValidatorInterface
     */
    protected IntegratorExecutionValidatorInterface $integratorExecutionValidator;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Generator\ViolationBodyMessageBuilder $violationBodyMessageBuilder
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \Upgrade\Application\Strategy\Common\IntegratorExecutionValidatorInterface $integratorExecutionValidator
     */
    public function __construct(
        ViolationBodyMessageBuilder $violationBodyMessageBuilder,
        ConfigurationProviderInterface $configurationProvider,
        IntegratorExecutionValidatorInterface $integratorExecutionValidator
    ) {
        $this->violationBodyMessageBuilder = $violationBodyMessageBuilder;
        $this->configurationProvider = $configurationProvider;
        $this->integratorExecutionValidator = $integratorExecutionValidator;
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
        $warningsSection = $this->buildBlockersWarnings($stepsResponseDto)
            . $this->buildViolationsWarnings($stepsResponseDto)
            . $this->buildIntegratorWarnings($stepsResponseDto);

        return $this->buildHeaderText($stepsResponseDto, $releaseGroupId)
            . PHP_EOL
            . $this->buildReleaseGroupsTable($this->configurationProvider, $stepsResponseDto, $releaseGroupId)
            . PHP_EOL
            . (trim($warningsSection) !== '' ? '## Warnings' . PHP_EOL : '')
            . $warningsSection
            . $this->buildListOfPackages($stepsResponseDto, $releaseGroupId)
            . $this->buildFooterText($stepsResponseDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     * @param int|null $releaseGroupId
     *
     * @return string
     */
    protected function buildHeaderText(StepsResponseDto $stepsResponseDto, ?int $releaseGroupId): string
    {
        $releaseGroupStatDto = $stepsResponseDto->getReleaseGroupStatDto();

        $text = sprintf('Upgrader installed %s release group(s) ', $releaseGroupStatDto->getAppliedRGsAmount());

        if ($releaseGroupStatDto->getAppliedSecurityFixesAmount()) {
            $text .= sprintf('(including %s security fix(s)) ', $releaseGroupStatDto->getAppliedSecurityFixesAmount());
        }

        $composerDiffDto = $stepsResponseDto->getComposerLockDiff();

        if ($composerDiffDto !== null) {
            $text .= sprintf(
                'containing %s package version(s)',
                count($composerDiffDto->getRequiredPackages()) + count($composerDiffDto->getRequiredDevPackages()),
            );
        }

        $text .= '.';

        if ($releaseGroupId !== null && $stepsResponseDto->getLastAppliedReleaseGroup() !== null) {
            $jiraIssueLink = $stepsResponseDto->getLastAppliedReleaseGroup()->getJiraIssueLink();

            $text .= sprintf(' Jira ticket [%s](%s).', $jiraIssueLink, $jiraIssueLink);
        }

        return $text;
    }

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $upgradeConfigurationProvider
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     * @param int|null $releaseGroupId
     *
     * @return string
     */
    protected function buildReleaseGroupsTable(
        ConfigurationProviderInterface $upgradeConfigurationProvider,
        StepsResponseDto $stepsResponseDto,
        ?int $releaseGroupId
    ): string {
        if (count($stepsResponseDto->getAppliedReleaseGroups()) === 0) {
            return '';
        }

        $isSequentialProcessor = $upgradeConfigurationProvider->getReleaseGroupProcessor(
        ) === ConfigurationProviderInterface::SEQUENTIAL_RELEASE_GROUP_PROCESSOR;

        $shouldDisplayWarningColumn = $releaseGroupId !== null || $isSequentialProcessor;
        $shouldDisplayRatingColumn = $this->integratorExecutionValidator->isIntegratorShouldBeInvoked();
        $manifestRatingThreshold = $this->configurationProvider->getManifestsRatingThreshold();

        $text = sprintf(
            '| Release |%s%s',
            $shouldDisplayRatingColumn ? ' Efforts saved by Upgrader |' : '',
            $shouldDisplayWarningColumn ? ' Warnings detected? |' : '',
        ) . PHP_EOL;

        $text .= sprintf(
            '| ------- |%s%s',
            $shouldDisplayRatingColumn ? ' ---- |' : '',
            $shouldDisplayWarningColumn ? ' ------------------ |' : '',
        ) . PHP_EOL;

        foreach ($stepsResponseDto->getAppliedReleaseGroups() as $appliedReleaseGroup) {
            $text .= sprintf(
                '| [%s](%s) |%s%s',
                $appliedReleaseGroup->getId(),
                $appliedReleaseGroup->getLink(),
                $shouldDisplayRatingColumn ? $this->buildRatingCell($appliedReleaseGroup, $manifestRatingThreshold) . ' |' : '',
                $shouldDisplayWarningColumn ? $this->getReleaseGroupsTableWarningColumnText(
                    $stepsResponseDto,
                    $appliedReleaseGroup,
                ) . ' |' : '',
            ) . PHP_EOL;
        }

        $text .= PHP_EOL;

        if (strpos($text, '*') !== false) {
            $text .= '\* This Release has too low coverage and cannot be automatically integrated.</sub>';
            $text .= PHP_EOL;
        }

        return $text;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $appliedReleaseGroup
     * @param int $manifestRatingThreshold
     *
     * @return string
     */
    protected function buildRatingCell(ReleaseGroupDto $appliedReleaseGroup, int $manifestRatingThreshold): string
    {
        return $appliedReleaseGroup->getRating() > 0 && $appliedReleaseGroup->getRating() < $manifestRatingThreshold
            ? sprintf('%s%%*', $appliedReleaseGroup->getRating())
            : sprintf('%s%%', $appliedReleaseGroup->getRating());
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $appliedReleaseGroup
     *
     * @return string
     */
    protected function getReleaseGroupsTableWarningColumnText(
        StepsResponseDto $stepsResponseDto,
        ReleaseGroupDto $appliedReleaseGroup
    ): string {
        return $this->responseHasWarnings($stepsResponseDto, $appliedReleaseGroup) ? 'Yes :warning:' : 'No';
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroupDto
     *
     * @return bool
     */
    protected function responseHasWarnings(StepsResponseDto $stepsResponseDto, ReleaseGroupDto $releaseGroupDto): bool
    {
        $releaseGroupId = $releaseGroupDto->getId();

        return count($stepsResponseDto->getBlockersByReleaseGroupId($releaseGroupId)) > 0
            || count($stepsResponseDto->getViolationsByReleaseGroupId($releaseGroupId)) > 0
            || (
                $stepsResponseDto->getIntegratorResponseDtoByReleaseGroupId($releaseGroupId) !== null
                && count($stepsResponseDto->getIntegratorResponseDtoByReleaseGroupId($releaseGroupId)->getWarnings()) > 0
            );
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return string
     */
    protected function buildBlockersWarnings(StepsResponseDto $stepsResponseDto): string
    {
        $text = '';
        $warnings = [];

        foreach ($stepsResponseDto->getBlockers() as $blockers) {
            foreach ($blockers as $blocker) {
                if (!isset($warnings[$blocker->getTitle()])) {
                    $warnings[$blocker->getTitle()] = [];
                }

                if (in_array($blocker->getMessage(), $warnings[$blocker->getTitle()], true)) {
                    continue;
                }

                $warnings[$blocker->getTitle()][] = $blocker->getMessage();
            }
        }

        if (count($warnings) === 0) {
            return '';
        }

        foreach ($warnings as $title => $messages) {
            $text .= sprintf('<details><summary><h4>%s</h4></summary>', $title);
            foreach ($messages as $message) {
                $text .= sprintf('<p>%s</p>', $message);
            }
            $text .= '</details>';
        }

        return $text . PHP_EOL;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return string
     */
    protected function buildViolationsWarnings(StepsResponseDto $stepsResponseDto): string
    {
        $composerDiffDto = $stepsResponseDto->getComposerLockDiff();

        if ($composerDiffDto === null) {
            return '';
        }

        /** @var array<\Upgrade\Application\Dto\ViolationDtoInterface> $violations */
        $violations = array_merge(...array_values($stepsResponseDto->getViolations()));

        if (count($violations) === 0) {
            return '';
        }

        /** @var array<\Upgrade\Application\Dto\ViolationDtoInterface> $uniqueViolations */
        $uniqueViolations = [];

        foreach ($violations as $violation) {
            foreach ($uniqueViolations as $uniqueViolation) {
                if ($uniqueViolation->equals($violation)) {
                    continue 2;
                }
            }

            $uniqueViolations[] = $violation;
        }

        return $this->violationBodyMessageBuilder->buildViolationsMessage(
            $uniqueViolations,
            array_merge(
                $composerDiffDto->getRequiredPackages(),
                $composerDiffDto->getRequiredDevPackages(),
            ),
        ) . PHP_EOL;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return string
     */
    protected function buildIntegratorWarnings(StepsResponseDto $stepsResponseDto): string
    {
        $text = '';

        /** @var array<string> $messages */
        $messages = array_unique(
            array_merge(
                ...array_map(
                    static fn (IntegratorResponseDto $integratorResponseDto): array => $integratorResponseDto->getWarnings(),
                    $stepsResponseDto->getIntegratorResponseCollection(),
                ),
            ),
        );

        if (count($messages) === 0) {
            return '';
        }

        $text .= '<details><summary><h4>We were mot able to integrate these module versions</h4></summary>';

        $text .= PHP_EOL . PHP_EOL;
        $text .= $this->buildSkippedManifestTable($messages);
        $text .= '</details>';

        return $text . PHP_EOL;
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
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     * @param int|null $releaseGroupId
     *
     * @return string
     */
    protected function buildListOfPackages(StepsResponseDto $stepsResponseDto, ?int $releaseGroupId): string
    {
        $text = '';

        $composerDiffDto = $stepsResponseDto->getComposerLockDiff();

        if ($composerDiffDto === null) {
            return $text;
        }

        if (count($composerDiffDto->getRequiredPackages()) === 0 && count($composerDiffDto->getRequiredDevPackages()) === 0) {
            return $text;
        }

        $text .= sprintf('<details%s><summary><h2>List of packages</h2></summary>', $releaseGroupId !== null ? ' open' : '') . PHP_EOL . PHP_EOL;

        if (count($composerDiffDto->getRequiredPackages()) > 0) {
            $text .= '**Packages upgraded:**' . PHP_EOL . PHP_EOL;
            $text .= $this->buildPackageDiffTable($composerDiffDto->getRequiredPackages());
            $text .= PHP_EOL;
        }

        if (count($composerDiffDto->getRequiredDevPackages()) > 0) {
            $text .= '**Packages dev upgraded:**' . PHP_EOL . PHP_EOL;
            $text .= $this->buildPackageDiffTable($composerDiffDto->getRequiredDevPackages());
            $text .= PHP_EOL;
        }

        $text .= '</details>' . PHP_EOL . PHP_EOL;

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
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return string
     */
    protected function buildFooterText(StepsResponseDto $stepsResponseDto): string
    {
        return '### Having trouble with Upgrader and going to contact Spryker?'
            . PHP_EOL
            . '- Check [Upgrader docs](https://docs.spryker.com/docs/scu/dev/spryker-code-upgrader.html)'
            . PHP_EOL
            . '- Please copy this report ID or content of this PR and send it to us. '
            . sprintf('Report ID: %s', $stepsResponseDto->getReportId() ?? 'n/a');
    }
}
