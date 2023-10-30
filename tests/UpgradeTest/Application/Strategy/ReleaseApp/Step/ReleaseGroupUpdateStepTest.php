<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Step;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReleaseApp\Application\Configuration\ReleaseAppConstant;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerPackagesDto;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Factory\ComposerViolationDtoFactory;
use Upgrade\Application\Strategy\Common\Module\BetaMajorModule\BetaMajorModulesFetcherInterface;
use Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapper;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher;
use Upgrade\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher\PackageManagerPackagesFetcherInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorResolver;
use Upgrade\Application\Strategy\ReleaseApp\Processor\SequentialReleaseGroupProcessor;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\BetaMajorPackageFilterItem;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\DevMasterPackageFilterItem;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilter;
use Upgrade\Application\Strategy\ReleaseApp\Step\ReleaseGroupUpdateStep;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup\ConflictValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup\MajorVersionValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\MajorThresholdValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\MinorThresholdValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\PatchThresholdValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\ReleaseGroupThresholdValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidator;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Infrastructure\Adapter\ReleaseAppClientAdapter;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\PackageManager\ComposerAdapter;

class ReleaseGroupUpdateStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testSequentialReleaseGroupProcessorFailedByConflicts(): void
    {
        // Arrange
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $this->buildReleaseGroupDtoCollection(true),
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor([], [], [new ConflictValidator()]),
            ),
            $this->createConfigurationProviderMock(),
            $this->createMock(LoggerInterface::class),
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
                'There are no packages for the update.',
                'No new required-dev packages',
                'Release group "RG2" contains module conflicts. Please follow the link below to find addition information about the conflict https://api.release.spryker.com/release-groups/view/2',
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
            $this->createConfigurationProviderMock(),
            $this->createMock(LoggerInterface::class),
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
                'There are no packages for the update.',
                'No new required-dev packages',
                'Applied required packages count: 1',
                'There are no packages for the update.',
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
                    [
                        new MajorThresholdValidator($configurationProvider),
                        new MinorThresholdValidator($configurationProvider),
                        new PatchThresholdValidator($configurationProvider),
                        new ReleaseGroupThresholdValidator($configurationProvider),
                    ],
                ),
            ),
            $this->createConfigurationProviderMock(),
            $this->createMock(LoggerInterface::class),
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
                'There are no packages for the update.',
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
            $this->createConfigurationProviderMock(),
            $this->createMock(LoggerInterface::class),
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
            $this->createConfigurationProviderMock(),
            $this->createMock(LoggerInterface::class),
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
                'There are no packages for the update.',
                'No new required-dev packages',
                'Amount of applied release groups: 1',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testRunReturnsResponseDtoWithStatData(): void
    {
        // Assert
        $rgDtoCollection = $this->buildReleaseGroupDtoCollection();
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $rgDtoCollection,
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor(),
            ),
            $this->createConfigurationProviderMock(),
            $this->createMock(LoggerInterface::class),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Arrange
        $this->assertSame(
            $rgDtoCollection->count(),
            $stepsResponseDto->getReleaseGroupStatDto()->getAvailableRgsAmount(),
            'Result DTO contains expected number of applied Release Groups.',
        );
    }

    /**
     * @return void
     */
    public function testRunWithWithBetaMajorReleasesThatNotInstalledInProjectShouldBeSkipped(): void
    {
        // Arrange
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMockForBetaMajorTests(false, null);
        $betaMajorModulesFetcher = $this->createMock(BetaMajorModulesFetcherInterface::class);

        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $this->buildReleaseGroupDtoCollectionByModules([
                    new ModuleDto('spryker/picking-lists-backend-api', '0.1.1', ReleaseAppConstant::MODULE_TYPE_MINOR),
                ]),
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor(
                    [new BetaMajorPackageFilterItem($packageManagerAdapterMock)],
                    [],
                    [new MajorVersionValidator($this->createConfigurationProviderMock(), $betaMajorModulesFetcher)],
                ),
            ),
            $this->createConfigurationProviderMock(),
            $this->createMock(LoggerInterface::class),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 1',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testRunWithWithBetaMajorReleasesThatInstalledInProjectShouldTriggerValidationError(): void
    {
        // Arrange
        $releaseGroupModules = [new ModuleDto('spryker/picking-lists-backend-api', '0.1.1', ReleaseAppConstant::MODULE_TYPE_MINOR)];

        $packageManagerAdapterMock = $this->createPackageManagerAdapterMockForBetaMajorTests(false, '0.0.1');
        $betaMajorModulesFetcher = $this->createMock(BetaMajorModulesFetcherInterface::class);
        $betaMajorModulesFetcher->method('getBetaMajorsNotInstalledInDev')->willReturn($releaseGroupModules);

        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $this->buildReleaseGroupDtoCollectionByModules($releaseGroupModules),
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor(
                    [new BetaMajorPackageFilterItem($packageManagerAdapterMock)],
                    [],
                    [new MajorVersionValidator($this->createConfigurationProviderMock(), $betaMajorModulesFetcher)],
                ),
            ),
            $this->createConfigurationProviderMock(),
            $this->createMock(LoggerInterface::class),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 1',
                'There is a major release available for module spryker/picking-lists-backend-api. Please follow the link'
                . " below to find all documentation needed to help you upgrade to the latest release \nhttps://api.release.spryker.com/release-groups/view/1",
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testRunWithWithManualChangesThatInstalledInProjectShouldTriggerValidationError(): void
    {
        // Arrange
        $releaseGroupDtoCollection = new ReleaseGroupDtoCollection(array_map(
            function (ReleaseGroupDto $releaseGroupDto) {
                $releaseGroupDto->setManualActionNeeded(true);

                return $releaseGroupDto;
            },
            $this->buildReleaseGroupDtoCollection()->toArray(),
        ));
        $betaMajorModulesFetcher = $this->createMock(BetaMajorModulesFetcherInterface::class);
        $step = new ReleaseGroupUpdateStep(
            $this->creteReleaseAppClientAdapterMock(
                $releaseGroupDtoCollection,
            ),
            $this->creteReleaseGroupProcessorResolverMock(
                $this->createSequentialReleaseGroupProcessor(
                    [],
                    [],
                    [new MajorVersionValidator($this->createConfigurationProviderMock(), $betaMajorModulesFetcher)],
                ),
            ),
            $this->createConfigurationProviderMock(),
            $this->createMock(LoggerInterface::class),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $stepsResponseDto = $step->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            implode(PHP_EOL, [
                'Amount of available release groups for the project: 2',
                'This release needs manual changes, please follow migration guide. Please follow the link below to find all documentation needed to help you upgrade to the latest release ',
                'https://api.release.spryker.com/release-groups/view/1',
            ]),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @param bool $isLockDevPackage
     * @param string|null $lockPackageVersion
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerAdapterMockForBetaMajorTests(bool $isLockDevPackage, ?string $lockPackageVersion): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->method('isLockDevPackage')->willReturn($isLockDevPackage);
        $packageManagerAdapter->method('getPackageVersion')->willReturn($lockPackageVersion);

        return $packageManagerAdapter;
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
        $composerAdapterMock->method('getReleaseGroup')->willReturn($releaseAppResponse);

        return $composerAdapterMock;
    }

    /**
     * @param array $releaseGroupFilters
     * @param array $thresholdSoftValidators
     * @param array $releaseGroupValidators
     *
     * @return \Upgrade\Application\Strategy\ReleaseApp\Processor\SequentialReleaseGroupProcessor
     */
    protected function createSequentialReleaseGroupProcessor(
        array $releaseGroupFilters = [],
        array $thresholdSoftValidators = [],
        array $releaseGroupValidators = []
    ): SequentialReleaseGroupProcessor {
        $responseDto = new PackageManagerResponseDto(true);

        $composerAdapterMock = $this->getMockBuilder(ComposerAdapter::class)
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $composerAdapterMock->method('require')->willReturn($responseDto);
        $composerAdapterMock->method('requireDev')->willReturn($responseDto);

        return new SequentialReleaseGroupProcessor(
            new ReleaseGroupSoftValidator($releaseGroupValidators),
            new ThresholdSoftValidator($thresholdSoftValidators),
            new ModuleFetcher(
                $composerAdapterMock,
                new PackageCollectionMapper(
                    $composerAdapterMock,
                ),
                $this->createPackageManagerPackagesFetcherMock(),
            ),
            new ReleaseGroupFilter($releaseGroupFilters),
            $this->createEventDispatcherMock(),
            $this->createMock(LoggerInterface::class),
            new ComposerViolationDtoFactory(),
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
                1,
                'RG1',
                new ModuleDtoCollection([
                    new ModuleDto('spryker/product-category', '4.17.0', 'minor'),
                ]),
                false,
                'https://api.release.spryker.com/release-groups/view/1',
                100,
            ),
            new ReleaseGroupDto(
                1,
                'RG2',
                new ModuleDtoCollection([
                    new ModuleDto('spryker/oauth-backend-api', '1.1.1', 'path'),
                ]),
                true,
                'https://api.release.spryker.com/release-groups/view/2',
                100,
                $conflictDetected,
            ),
        ]);
    }

    /**
     * @param array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto> $moduleDtoCollection
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection
     */
    protected function buildReleaseGroupDtoCollectionByModules(array $moduleDtoCollection): ReleaseGroupDtoCollection
    {
        return new ReleaseGroupDtoCollection([
            new ReleaseGroupDto(
                1,
                'RG1',
                new ModuleDtoCollection($moduleDtoCollection),
                false,
                'https://api.release.spryker.com/release-groups/view/1',
                100,
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

    /**
     * @return \Upgrade\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher\PackageManagerPackagesFetcherInterface
     */
    protected function createPackageManagerPackagesFetcherMock(): PackageManagerPackagesFetcherInterface
    {
        return new class () implements PackageManagerPackagesFetcherInterface
        {
            /**
             * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
             *
             * @return \Upgrade\Application\Dto\PackageManagerPackagesDto
             */
            public function fetchPackages(PackageCollection $packageCollection): PackageManagerPackagesDto
            {
                return new PackageManagerPackagesDto($packageCollection, new PackageCollection([]), new PackageCollection([]));
            }
        };
    }

    /**
     * @return \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
     */
    protected function createEventDispatcherMock(): EventDispatcherInterface
    {
        return $this->createMock(EventDispatcherInterface::class);
    }
}
