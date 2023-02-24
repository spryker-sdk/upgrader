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
use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapper;
use Upgrade\Application\Strategy\ReleaseApp\Processor\AggregateReleaseGroupProcessor;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ModulePackageFetcher;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorResolver;
use Upgrade\Application\Strategy\ReleaseApp\Processor\SequentialReleaseGroupProcessor;
use Upgrade\Application\Strategy\ReleaseApp\Step\ReleaseGroupUpdateStep;
use Upgrade\Application\Strategy\ReleaseApp\Validator\PackageSoftValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidator;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidator;
use Upgrade\Infrastructure\Adapter\ReleaseAppClientAdapter;
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
            new ReleaseGroupSoftValidator([]),
            new ThresholdSoftValidator([]),
            new ModulePackageFetcher(
                $composerAdapterMock,
                new PackageCollectionMapper(
                    new PackageSoftValidator([]),
                    $composerAdapterMock,
                ),
            ),
        );
    }

    /**
     * @return \Upgrade\Application\Strategy\ReleaseApp\Processor\SequentialReleaseGroupProcessor
     */
    protected function createSequentialReleaseGroupProcessor(): SequentialReleaseGroupProcessor
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

        return new SequentialReleaseGroupProcessor(
            new ReleaseGroupSoftValidator([]),
            new ThresholdSoftValidator([]),
            new ModulePackageFetcher(
                $composerAdapterMock,
                new PackageCollectionMapper(
                    new PackageSoftValidator([]),
                    $composerAdapterMock,
                ),
            ),
        );
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection
     */
    protected function buildReleaseGroupDtoCollection(): ReleaseGroupDtoCollection
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
            ),
        ]);
    }
}
