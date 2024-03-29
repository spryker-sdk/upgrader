<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

use ReleaseApp\Application\Service\ReleaseAppServiceInterface;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\IntegratorResponseDto;
use Upgrade\Application\Dto\ReleaseGroupFilterResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\Common\IntegratorExecutionValidatorInterface;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProvider;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

/**
 * @codeCoverageIgnore don't need cover presentation view class
 */
class PullRequestDataGenerator
{
    /**
     * @var int
     */
    protected const INTEGRATION_GUIDE_LENGTH = 500;

    /**
     * @var int
     */
    protected const RELEASE_GROUP_TITLE_LENGTH = 50;

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
     * @var \ReleaseApp\Application\Service\ReleaseAppServiceInterface $releaseApp
     */
    protected ReleaseAppServiceInterface $releaseApp;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface
     */
    protected SourceCodeProviderInterface $sourceCodeProvider;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Generator\ViolationBodyMessageBuilder $violationBodyMessageBuilder
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \Upgrade\Application\Strategy\Common\IntegratorExecutionValidatorInterface $integratorExecutionValidator
     * @param \ReleaseApp\Application\Service\ReleaseAppServiceInterface $releaseApp
     * @param \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProvider $sourceCodeProvider
     */
    public function __construct(
        ViolationBodyMessageBuilder $violationBodyMessageBuilder,
        ConfigurationProviderInterface $configurationProvider,
        IntegratorExecutionValidatorInterface $integratorExecutionValidator,
        ReleaseAppServiceInterface $releaseApp,
        SourceCodeProvider $sourceCodeProvider
    ) {
        $this->violationBodyMessageBuilder = $violationBodyMessageBuilder;
        $this->configurationProvider = $configurationProvider;
        $this->integratorExecutionValidator = $integratorExecutionValidator;
        $this->releaseApp = $releaseApp;
        $this->sourceCodeProvider = $sourceCodeProvider->getSourceCodeProvider();
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
        $warningsSection = $this->buildBlockers($stepsResponseDto)
            . $this->buildProjectViolationWarnings($stepsResponseDto)
            . $this->buildViolationsWarnings($stepsResponseDto)
            . $this->buildIntegratorWarnings($stepsResponseDto);

        $hasWarnings = trim($warningsSection) !== '';

        return $this->buildHeaderText($stepsResponseDto, $releaseGroupId, $hasWarnings)
            . PHP_EOL
            . $this->buildReleaseGroupsTable($stepsResponseDto)
            . PHP_EOL
            . ($hasWarnings ? '## ' . $this->createErrorTitle($stepsResponseDto) . PHP_EOL : '')
            . $warningsSection
            . PHP_EOL
            . $this->buildReleaseGroupIntegrationGuideTable($stepsResponseDto)
            . PHP_EOL
            . $this->buildListOfPackages($stepsResponseDto, $releaseGroupId)
            . PHP_EOL
            . $this->buildFooterText($stepsResponseDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     * @param int|null $releaseGroupId
     * @param bool $hasWarnings
     *
     * @return string
     */
    protected function buildHeaderText(StepsResponseDto $stepsResponseDto, ?int $releaseGroupId, bool $hasWarnings): string
    {
        if ($hasWarnings && count($stepsResponseDto->getAppliedReleaseGroups()) === 0) {
            return 'Unfortunately Upgrader was not able to create a pull request with code changes due to errors. Please see the list below and resolve them.';
        }

        $releaseGroupStatDto = $stepsResponseDto->getReleaseGroupStatDto();
        $composerDiffDto = $stepsResponseDto->getComposerLockDiff();
        $countOfPackages = 0;
        if ($composerDiffDto !== null) {
            $countOfPackages = count($composerDiffDto->getRequiredPackages()) + count($composerDiffDto->getRequiredDevPackages());
        }
        $countOfRGs = $releaseGroupStatDto->getAppliedRGsAmount();
        if (!$countOfRGs && $countOfPackages) {
            $countOfRGs = 1;
        }
        $text = sprintf('Upgrader installed %s release group(s) ', $countOfRGs);

        if ($releaseGroupStatDto->getAppliedSecurityFixesAmount()) {
            $text .= sprintf('(including %s security fix(es)) ', $releaseGroupStatDto->getAppliedSecurityFixesAmount());
        }

        if ($countOfPackages) {
            $text .= sprintf(
                'containing %s package version(s)',
                $countOfPackages,
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
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return string
     */
    protected function buildReleaseGroupsTable(StepsResponseDto $stepsResponseDto): string
    {
        if (count($stepsResponseDto->getAppliedReleaseGroups()) === 0) {
            return '';
        }

        $shouldDisplayRatingColumn = $this->integratorExecutionValidator->isIntegratorShouldBeInvoked();
        $shouldDisplayModuleOfferColumn = $this->shouldDisplayModuleOfferColumn($stepsResponseDto->getFilterResponseList());
        $manifestRatingThreshold = $this->configurationProvider->getManifestsRatingThreshold();

        $text = sprintf(
            '| Release |%s%s%s',
            $shouldDisplayRatingColumn ? ' Efforts saved by Upgrader |' : '',
            ' Warnings detected? |',
            $shouldDisplayModuleOfferColumn ? ' These new modules might be interesting for you |' : '',
        ) . PHP_EOL;

        $text .= sprintf(
            '| ------- |%s%s%s',
            $shouldDisplayRatingColumn ? ' ---- |' : '',
            ' ------------------ |',
            $shouldDisplayModuleOfferColumn ? ' ------------------ |' : '',
        ) . PHP_EOL;

        foreach ($stepsResponseDto->getAppliedReleaseGroups() as $appliedReleaseGroup) {
            $text .= sprintf(
                '| [%s](%s) |%s%s%s',
                $this->getReleaseGroupName($appliedReleaseGroup),
                $appliedReleaseGroup->getLink(),
                $shouldDisplayRatingColumn ? $this->buildRatingCell($appliedReleaseGroup, $manifestRatingThreshold) . ' |' : '',
                $this->getReleaseGroupsTableWarningColumnText($stepsResponseDto, $appliedReleaseGroup) . ' |',
                $shouldDisplayModuleOfferColumn ? $this->buildModuleOfferCell($stepsResponseDto->getFilterResponseList(), $appliedReleaseGroup) . ' |' : '',
            ) . PHP_EOL;
        }

        $text .= PHP_EOL;

        if (strpos($text, '*') !== false) {
            $text .= '\* This Release has too low coverage and cannot be automatically integrated.</sub>';
            $text .= PHP_EOL;
        }

        /** @var \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $firstAppliedRG */
        $firstAppliedRG = $stepsResponseDto->getAppliedReleaseGroups()[0] ?? null;
        if ($firstAppliedRG) {
            $text .= sprintf(
                'There are also [releases](%s), that did not include any modules, please check them out.',
                $this->releaseApp->getReleaseHistoryLink('released', 'asc', $firstAppliedRG->getReleased(), true),
            );
            $text .= PHP_EOL;
        }

        return $text;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroupDto
     *
     * @return string
     */
    protected function getReleaseGroupName(ReleaseGroupDto $releaseGroupDto): string
    {
        $name = trim((string)preg_replace(
            '/^' . preg_quote((string)$releaseGroupDto->getJiraIssue(), '/') . '\b/i',
            '',
            $releaseGroupDto->getName(),
        ));

        return mb_strlen($name) > static::RELEASE_GROUP_TITLE_LENGTH
            ? mb_substr($name, 0, static::RELEASE_GROUP_TITLE_LENGTH) . '...'
            : $name;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return string
     */
    protected function createErrorTitle(StepsResponseDto $stepsResponseDto): string
    {
        return count($stepsResponseDto->getAppliedReleaseGroups()) > 0 ? 'Warnings' : 'Errors :warning:';
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return string
     */
    protected function buildReleaseGroupIntegrationGuideTable(StepsResponseDto $stepsResponseDto): string
    {
        if (
            count(array_filter(
                $stepsResponseDto->getAppliedReleaseGroups(),
                fn (ReleaseGroupDto $appliedReleaseGroup): ?string => $appliedReleaseGroup->getIntegrationGuide()
            )) === 0
        ) {
            return '';
        }
        $text = '<details><summary><h2>Integration Instructions</h2></summary>' . PHP_EOL . PHP_EOL;

        foreach ($stepsResponseDto->getAppliedReleaseGroups() as $appliedReleaseGroup) {
            $integrationGuide = $appliedReleaseGroup->getIntegrationGuide();
            if (!$integrationGuide) {
                continue;
            }
            if (mb_strlen($integrationGuide) > static::INTEGRATION_GUIDE_LENGTH) {
                $integrationGuide = mb_substr($integrationGuide, 0, static::INTEGRATION_GUIDE_LENGTH);
                $integrationGuide .= sprintf('... [read more](%s)', $appliedReleaseGroup->getLink());
            }
            $text .= sprintf('[%s](%s) - %s', $appliedReleaseGroup->getId(), $appliedReleaseGroup->getLink(), $integrationGuide) . PHP_EOL . PHP_EOL;
        }
        $text .= '</details>' . PHP_EOL . PHP_EOL;

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
     * @param array<\Upgrade\Application\Dto\ReleaseGroupFilterResponseDto> $filterResponseList
     *
     * @return bool
     */
    protected function shouldDisplayModuleOfferColumn(array $filterResponseList): bool
    {
        return count(array_filter($filterResponseList, static fn (ReleaseGroupFilterResponseDto $filterResponse): bool => !$filterResponse->getProposedModuleCollection()->isEmpty())) > 0;
    }

    /**
     * @param array<\Upgrade\Application\Dto\ReleaseGroupFilterResponseDto> $filterResponseList
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $appliedReleaseGroup
     *
     * @return string
     */
    protected function buildModuleOfferCell(array $filterResponseList, ReleaseGroupDto $appliedReleaseGroup): string
    {
        $moduleList = [];
        foreach ($filterResponseList as $filterResponse) {
            if ($filterResponse->getReleaseGroupDto()->getId() !== $appliedReleaseGroup->getId()) {
                continue;
            }
            foreach ($filterResponse->getProposedModuleCollection()->toArray() as $moduleOffer) {
                $moduleList[] = sprintf('[%s:%s](https://github.com/%s)', $moduleOffer->getName(), $moduleOffer->getVersion(), $moduleOffer->getName());
            }
        }

        return implode('<br>', array_unique($moduleList));
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return string
     */
    protected function buildBlockers(StepsResponseDto $stepsResponseDto): string
    {
        $message = '';
        foreach ($stepsResponseDto->getBlockers() as $blockers) {
            foreach ($blockers as $blocker) {
                $message .= $this->sourceCodeProvider->buildBlockerTextBlock($blocker);
            }
        }

        return $message;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return string
     */
    protected function buildProjectViolationWarnings(StepsResponseDto $stepsResponseDto): string
    {
        $warnings = [];

        foreach ($stepsResponseDto->getProjectViolations() as $projectViolation) {
            $warnings = $this->addValidatorViolationIntoWarnings($warnings, $projectViolation);
        }

        if (count($warnings) === 0) {
            return '';
        }

        return $this->buildWarningsTextBlocks($warnings) . PHP_EOL;
    }

    /**
     * @param array<string, array<string>> $warnings
     * @param \Upgrade\Application\Dto\ValidatorViolationDto $validatorViolationDto
     *
     * @return array<string, array<string>>
     */
    protected function addValidatorViolationIntoWarnings(array $warnings, ValidatorViolationDto $validatorViolationDto): array
    {
        if (!isset($warnings[$validatorViolationDto->getTitle()])) {
            $warnings[$validatorViolationDto->getTitle()] = [];
        }

        if (in_array($validatorViolationDto->getMessage(), $warnings[$validatorViolationDto->getTitle()], true)) {
            return $warnings;
        }

        $warnings[$validatorViolationDto->getTitle()][] = $validatorViolationDto->getMessage();

        return $warnings;
    }

    /**
     * @param array<string, array<string>> $warnings
     *
     * @return string
     */
    protected function buildWarningsTextBlocks(array $warnings): string
    {
        $text = '';

        foreach ($warnings as $title => $messages) {
            $text .= sprintf('<details><summary><h4>%s</h4></summary>', $title);
            foreach ($messages as $message) {
                $text .= sprintf('<p>%s</p>', nl2br($message));
            }
            $text .= '</details>';
        }

        return $text;
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

        $text .= '<details><summary><h4>We were not able to integrate these module versions</h4></summary>';

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
            $skippedManifest = trim($skippedManifest);

            $skippedManifestHash = md5($skippedManifest);

            if (in_array($skippedManifestHash, $processedSkippedManifests, true)) {
                continue;
            }

            $processedSkippedManifests[] = $skippedManifestHash;

            preg_match('/[a-zA-Z]*:[0-9]*.[0-9]*.[0-9]*/', $skippedManifest, $matches);
            if (!count($matches)) {
                $row = implode(' | ', ['', '-', '-', $skippedManifest, PHP_EOL]);
                $text .= $row;

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
            . '- Check [Upgrader docs](https://docs.spryker.com/docs/ca/devscu/spryker-code-upgrader.html)'
            . PHP_EOL
            . '- Please copy this report ID or content of this PR and send it to us. '
            . sprintf('Report ID: %s', $stepsResponseDto->getReportId() ?? 'n/a');
    }
}
