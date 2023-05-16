<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Step;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Executor\StepExecutor;
use Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapper;
use Upgrade\Application\Strategy\ReleaseApp\Processor\AggregateReleaseGroupProcessor;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorResolver;
use Upgrade\Application\Strategy\ReleaseApp\Processor\SequentialReleaseGroupProcessor;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\DevMasterPackageFilterItem;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilter;
use Upgrade\Application\Strategy\ReleaseApp\Step\ReleaseGroupUpdateStep;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup\ConflictValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\MajorThresholdValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\MinorThresholdValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\PatchThresholdValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\ReleaseGroupThresholdValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidator;
use Upgrade\Infrastructure\Adapter\ReleaseAppClientAdapter;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\PackageManager\ComposerAdapter;

class ReleaseGroupUpdateStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testRunWithAggregateReleaseGroupProcessor(): void
    {
        // Arrange
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $this->buildReleaseGroupDtoCollection(),
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createAggregateReleaseGroupProcessor(),
            ),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 2',
                'Applied required packages count: 2',
                'No new required-dev packages',
                'Amount of applied release groups: 2',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testProcessWithAggregateReleaseGroupProcessorAndEmptyReleaseGroupDtoCollection(): void
    {
        // Arrange
        $releaseGroupDtoCollection = new ReleaseGroupDtoCollection();
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock($releaseGroupDtoCollection),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createAggregateReleaseGroupProcessor(),
            ),
        );
        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 0',
                'No valid packages found',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testAggregateReleaseGroupProcessorFailedByConflicts(): void
    {
        // Arrange
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $this->buildReleaseGroupDtoCollection(true),
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createAggregateReleaseGroupProcessor(),
            ),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 2',
                'Release group "RG2" contains module conflicts. Please follow the link below to find addition information about the conflict https://api.release.spryker.com/release-groups/view/2',
                'Applied required packages count: 1',
                'No new required-dev packages',
                'Amount of applied release groups: 1',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testRunWithSequentialReleaseGroupProcessor(): void
    {
        // Arrange
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $this->buildReleaseGroupDtoCollection(),
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor(),
            ),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 2',
                'Applied required packages count: 1',
                'No new required-dev packages',
                'Applied required packages count: 1',
                'No new required-dev packages',
                'Amount of applied release groups: 2',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testRunWithSequentialReleaseGroupProcessorApplySoftThreshold(): void
    {
        // Arrange
        $configurationProvider = $this->createConfigurationProviderMock();
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $this->buildReleaseGroupDtoCollection(),
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor(
                    [],
                    [],
                    [
                        new MajorThresholdValidator($configurationProvider),
                        new MinorThresholdValidator($configurationProvider),
                        new PatchThresholdValidator($configurationProvider),
                        new ReleaseGroupThresholdValidator($configurationProvider),
                    ],
                ),
            ),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 2',
                'Applied required packages count: 1',
                'No new required-dev packages',
                'Soft threshold hit by 1 minor releases',
                'Amount of applied release groups: 1',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testRunWithSequentialReleaseGroupProcessorApplySoftThreshold(): void
    {
        // Arrange
        $configurationProvider = $this->createConfigurationProviderMock();
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $this->buildReleaseGroupDtoCollection(),
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor(
                    [],
                    [
                        new MajorThresholdValidator($configurationProvider),
                        new MinorThresholdValidator($configurationProvider),
                        new PatchThresholdValidator($configurationProvider),
                        new ReleaseGroupThresholdValidator($configurationProvider),
                    ],
                ),
            ),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 2',
                'Applied required packages count: 1',
                'No new required-dev packages',
                'Soft threshold hit by 1 minor releases',
                'Amount of applied release groups: 1',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testProcessWithSequentialReleaseGroupProcessorAndEmptyReleaseGroupDtoCollection(): void
    {
        // Arrange
        $releaseGroupDtoCollection = new ReleaseGroupDtoCollection();
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock($releaseGroupDtoCollection),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor(),
            ),
        );
        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 0',
                'The branch is up to date. No further action is required.',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testDevMasterFilterShouldFilterReleaseAppPackages(): void
    {
        // Arrange
        $releaseGroupFilters = [
            new DevMasterPackageFilterItem($this->createPackageManagerAdapterMock(['require' => ['spryker/product-category' => 'dev-master']])),
        ];

        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $this->buildReleaseGroupDtoCollection(),
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor($releaseGroupFilters),
            ),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 2',
                'No valid packages found',
                'Applied required packages count: 1',
                'No new required-dev packages',
                'Amount of applied release groups: 2',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorInterface $processor
     *
     * @return \Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorResolver
     */
    protected function creteReleaseGroupProcessorResolverMock(ReleaseGroupProcessorInterface $processor): ReleaseGroupProcessorResolver
    {
        $composerAdapterMock = $this->getMockBuilder(ReleaseGroupProcessorResolver::class)
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $composerAdapterMock->method('getProcessor')->willReturn($processor);

        return $composerAdapterMock;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $releaseGroupDtoCollection
     *
     * @return \Upgrade\Infrastructure\Adapter\ReleaseAppClientAdapter
     */
    protected function creteReleaseAppClientAdapterMock(ReleaseGroupDtoCollection $releaseGroupDtoCollection): ReleaseAppClientAdapter
    {
        $releaseAppResponse = new ReleaseAppResponse($releaseGroupDtoCollection);
        $composerAdapterMock = $this->getMockBuilder(ReleaseAppClientAdapter::class)
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $composerAdapterMock->method('getNewReleaseGroups')->willReturn($releaseAppResponse);

        return $composerAdapterMock;
    }

    /**
     * @return \Upgrade\Application\Strategy\ReleaseApp\Processor\AggregateReleaseGroupProcessor
     */
    protected function createAggregateReleaseGroupProcessor(): AggregateReleaseGroupProcessor
    {
        $responseDto = new ResponseDto(true);

        $composerAdapterMock = $this->getMockBuilder(ComposerAdapter::class)
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $composerAdapterMock->method('require')->willReturn($responseDto);
        $composerAdapterMock->method('requireDev')->willReturn($responseDto);
        $composerAdapterMock->method('isDevPackage')->willReturn(false);

        return new AggregateReleaseGroupProcessor(
            new ReleaseGroupSoftValidator([
                new ConflictValidator(),
            ]),
            new ThresholdSoftValidator([]),
            new ModuleFetcher(
                $composerAdapterMock,
                new PackageCollectionMapper(
                    $composerAdapterMock,
                ),
            ),
            new ReleaseGroupFilter([]),
            new StepExecutor(),
            new StepExecutor(),
        );
    }

    /**
     * @param array $releaseGroupFilters
     * @param array $preRequireProcessorStrategies
     * @param array $thresholdSoftValidators
     *
     * @return \Upgrade\Application\Strategy\ReleaseApp\Processor\SequentialReleaseGroupProcessor
     */
    protected function createSequentialReleaseGroupProcessor(
        array $releaseGroupFilters = [],
        array $preRequireProcessorStrategies = [],
        array $thresholdSoftValidators = []
    ): SequentialReleaseGroupProcessor {
        $responseDto = new ResponseDto(true);

        $composerAdapterMock = $this->getMockBuilder(ComposerAdapter::class)
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $composerAdapterMock->method('require')->willReturn($responseDto);
        $composerAdapterMock->method('requireDev')->willReturn($responseDto);
        $composerAdapterMock->method('isDevPackage')->willReturn(false);

        return new SequentialReleaseGroupProcessor(
            new ReleaseGroupSoftValidator([]),
            new ThresholdSoftValidator($thresholdSoftValidators),
            new ModulePackageFetcher(
                $composerAdapterMock,
                new PackageCollectionMapper(
                    $composerAdapterMock,
                ),
            ),
            new ReleaseGroupFilter($releaseGroupFilters),
            new StepExecutor(),
            new StepExecutor(),
        );
    }

    /**
     * @param bool $conflictDetected
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection
     */
    protected function buildReleaseGroupDtoCollection(bool $conflictDetected = false): ReleaseGroupDtoCollection
    {
        return new ReleaseGroupDtoCollection([
            new ReleaseGroupDto(
                'RG1',
                new ModuleDtoCollection([
                    new ModuleDto('spryker/product-category', '4.17.0', 'minor'),
                ]),
                false,
                'https://api.release.spryker.com/release-groups/view/1',
            ),
            new ReleaseGroupDto(
                'RG2',
                new ModuleDtoCollection([
                    new ModuleDto('spryker/oauth-backend-api', '1.1.1', 'path'),
                ]),
                true,
                'https://api.release.spryker.com/release-groups/view/2',
                $conflictDetected,
            ),
        ]);
    }

    /**
     * @param array<string, mixed> $composerJson
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerAdapterMock(array $composerJson = []): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->method('getComposerJsonFile')->willReturn($composerJson);

        return $packageManagerAdapter;
    }

    /**
     * @param string $package
     * @param string $version
     * @param array<string, mixed> $composerJson
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerPackageVersionAdapterMock(string $package, string $version, array $composerJson = []): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter
            ->method('getPackageVersion')
            ->with($package)->willReturn($version);
        $packageManagerAdapter
            ->method('getComposerJsonFile')
            ->willReturn($composerJson);

        return $packageManagerAdapter;
    }

    /**
     * @return \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected function createConfigurationProviderMock(): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);

        $configurationProvider->method('getSoftThresholdMajor')->willReturn(0);
        $configurationProvider->method('getSoftThresholdMinor')->willReturn(1);
        $configurationProvider->method('getSoftThresholdPatch')->willReturn(1);
        $configurationProvider->method('getThresholdReleaseGroup')->willReturn(1);

        return $configurationProvider;
    }
}
