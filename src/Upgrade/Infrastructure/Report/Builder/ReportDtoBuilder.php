<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Builder;

use DateTimeImmutable;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Report\Dto\ReportDto;
use Upgrade\Infrastructure\Report\Dto\ReportMetadataDto;
use Upgrade\Infrastructure\Report\Dto\ReportPayloadDto;

class ReportDtoBuilder implements ReportDtoBuilderInterface
{
    /**
     * @var string
     */
    public const REPORT_NAME = 'upgrader_report';

    /**
     * @var string
     */
    public const REPORT_SCOPE = 'Upgrader';

    /**
     * @var int
     */
    public const REPORT_VERSION = 1;

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Infrastructure\Report\Dto\ReportDto
     */
    public function buildFromStepResponseDto(StepsResponseDto $stepsResponseDto): ReportDto
    {
        return new ReportDto(
            static::REPORT_NAME,
            static::REPORT_VERSION,
            static::REPORT_SCOPE,
            new DateTimeImmutable(),
            $this->createReportPayload($stepsResponseDto),
            $this->createReportMetadata($stepsResponseDto),
        );
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Infrastructure\Report\Dto\ReportMetadataDto
     */
    protected function createReportMetadata(StepsResponseDto $stepsResponseDto): ReportMetadataDto
    {
        return new ReportMetadataDto(
            $this->configurationProvider->getOrganizationName(),
            $this->configurationProvider->getRepositoryName(),
            $this->configurationProvider->getGitLabProjectId(),
            $this->configurationProvider->getSourceCodeProvider(),
            $this->configurationProvider->getExecutionEnv(),
            (string)$stepsResponseDto->getReportId(),
        );
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Infrastructure\Report\Dto\ReportPayloadDto
     */
    protected function createReportPayload(StepsResponseDto $stepsResponseDto): ReportPayloadDto
    {
        $composerLockDiff = $stepsResponseDto->getComposerLockDiff();

        if ($composerLockDiff === null) {
            return new ReportPayloadDto();
        }

        $integratorResponse = $stepsResponseDto->getIntegratorResponseDto();

        return new ReportPayloadDto(
            $composerLockDiff->getRequireChanges(),
            $composerLockDiff->getRequireDevChanges(),
            $integratorResponse !== null ? $integratorResponse->getWarnings() : [],
        );
    }
}
