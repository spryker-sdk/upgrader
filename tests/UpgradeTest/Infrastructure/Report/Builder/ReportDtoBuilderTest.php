<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\Report\Builder;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\IntegratorResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Domain\Entity\Package;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Report\Builder\ReportDtoBuilder;

/**
 * @group UpgradeTest
 * @group Infrastructure
 * @group Report
 * @group Builder
 * @group ReportDtoBuilderTest
 */
class ReportDtoBuilderTest extends TestCase
{
    /**
     * @var string
     */
    protected const ORGANIZATION_NAME = 'vendor';

    /**
     * @var string
     */
    protected const REPOSITORY_NAME = 'suite';

    /**
     * @var string
     */
    protected const PROJECT_ID = 'project_id';

    /**
     * @var string
     */
    protected const SOURCE_CODE_PROVIDER = 'github';

    /**
     * @var string
     */
    protected const APP_ENV = 'CI';

    /**
     * @var string
     */
    protected const REPORT_ID = '902072d8-a2cc-11ed-a8fc-0242ac120002';

    /**
     * @return void
     */
    public function testBuildFromStepResponseDtoShouldReturnReportDto(): void
    {
        // Arrange
        $stepsResponseDto = $this->createStepsResponseDto($this->createComposerLockDiffDto());
        $reportDtoBuilder = new ReportDtoBuilder($this->createConfigurationProviderMock());

        // Act
        $reportDto = $reportDtoBuilder->buildFromStepResponseDto($stepsResponseDto);

        // Assert
        $this->assertSame(ReportDtoBuilder::REPORT_NAME, $reportDto->getName());
        $this->assertSame(ReportDtoBuilder::REPORT_VERSION, $reportDto->getVersion());
        $this->assertSame(ReportDtoBuilder::REPORT_SCOPE, $reportDto->getScope());

        $payload = $reportDto->getPayload();
        $this->assertCount(1, $payload->getRequiredPackages());
        $this->assertSame('spryker/category', $payload->getRequiredPackages()[0]->getName());
        $this->assertSame('1.0.0', $payload->getRequiredPackages()[0]->getPreviousVersion());
        $this->assertSame('1.0.1', $payload->getRequiredPackages()[0]->getVersion());
        $this->assertSame(
            'https://github.com/spryker/category/compare/1.0.0...1.0.1',
            $payload->getRequiredPackages()[0]->getDiffLink(),
        );

        $this->assertCount(1, $payload->getDevRequiredPackages());
        $this->assertSame('spryker/testify', $payload->getDevRequiredPackages()[0]->getName());
        $this->assertSame('2.0.0', $payload->getDevRequiredPackages()[0]->getPreviousVersion());
        $this->assertSame('2.0.1', $payload->getDevRequiredPackages()[0]->getVersion());
        $this->assertSame(
            'https://github.com/spryker/testify/compare/2.0.0...2.0.1',
            $payload->getDevRequiredPackages()[0]->getDiffLink(),
        );

        $this->assertSame(['Warning One', 'Warning Two'], $payload->getIntegratorWarnings());

        $metadata = $reportDto->getMetadata();
        $this->assertSame(static::REPORT_ID, $metadata->getReportId());
        $this->assertSame(static::APP_ENV, $metadata->getAppEnv());
        $this->assertSame(static::SOURCE_CODE_PROVIDER, $metadata->getSourceCodeProvider());
        $this->assertSame(static::PROJECT_ID, $metadata->getProjectId());
        $this->assertSame(static::ORGANIZATION_NAME, $metadata->getOrganizationName());
        $this->assertSame(static::REPOSITORY_NAME, $metadata->getRepositoryName());
    }

    /**
     * @return void
     */
    public function testBuildFromStepResponseDtoShouldReturnEmptyPayloadWhenComposerLockDiffNotSet(): void
    {
        // Arrange
        $stepsResponseDto = $this->createStepsResponseDto();
        $reportDtoBuilder = new ReportDtoBuilder($this->createConfigurationProviderMock());

        // Act
        $reportDto = $reportDtoBuilder->buildFromStepResponseDto($stepsResponseDto);

        // Assert
        $this->assertCount(0, $reportDto->getPayload()->getRequiredPackages());
        $this->assertCount(0, $reportDto->getPayload()->getDevRequiredPackages());
    }

    /**
     * @return \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected function createConfigurationProviderMock(): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);

        $configurationProvider->method('getOrganizationName')->willReturn(static::ORGANIZATION_NAME);
        $configurationProvider->method('getRepositoryName')->willReturn(static::REPOSITORY_NAME);
        $configurationProvider->method('getProjectId')->willReturn(static::PROJECT_ID);
        $configurationProvider->method('getSourceCodeProvider')->willReturn(static::SOURCE_CODE_PROVIDER);
        $configurationProvider->method('getAppEnv')->willReturn(static::APP_ENV);

        return $configurationProvider;
    }

    /**
     * @param \Upgrade\Application\Dto\ComposerLockDiffDto|null $composerLockDiffDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    protected function createStepsResponseDto(?ComposerLockDiffDto $composerLockDiffDto = null): StepsResponseDto
    {
        $stepsResponseDto = new StepsResponseDto();

        if ($composerLockDiffDto !== null) {
            $stepsResponseDto->setComposerLockDiff($composerLockDiffDto);
        }

        $stepsResponseDto->setReportId(static::REPORT_ID);

        $releaseGroupDtoMock = $this->createMock(ReleaseGroupDto::class);
        $releaseGroupDtoMock->method('getId')->willReturn(1);
        $stepsResponseDto->setCurrentReleaseGroup($releaseGroupDtoMock);
        $stepsResponseDto->addIntegratorResponseDto(new IntegratorResponseDto(['warning-list' => ['Warning One']]));

        $releaseGroupDtoMock = $this->createMock(ReleaseGroupDto::class);
        $releaseGroupDtoMock->method('getId')->willReturn(2);
        $stepsResponseDto->setCurrentReleaseGroup($releaseGroupDtoMock);
        $stepsResponseDto->addIntegratorResponseDto(new IntegratorResponseDto(['warning-list' => ['Warning Two']]));

        return $stepsResponseDto;
    }

    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto
     */
    protected function createComposerLockDiffDto(): ComposerLockDiffDto
    {
        return new ComposerLockDiffDto(
            [
                new Package('spryker/category', '1.0.1', '1.0.0', 'https://github.com/spryker/category/compare/1.0.0...1.0.1'),
            ],
            [
                new Package('spryker/testify', '2.0.1', '2.0.0', 'https://github.com/spryker/testify/compare/2.0.0...2.0.1'),
            ],
        );
    }
}
