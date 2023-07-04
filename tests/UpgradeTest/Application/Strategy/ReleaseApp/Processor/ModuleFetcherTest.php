<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class ModuleFetcherTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testRequireReturnsProperResponseDtoIfNothingToInstall(): void
    {
        // Assert
        $packageCollectionMapper = $this->createMock(PackageCollectionMapperInterface::class);
        $packageCollectionMapper->expects($this->once())
            ->method('mapModuleCollectionToPackageCollection')
            ->willReturn(new PackageCollection());

        $moduleFetcher = new ModuleFetcher(
            $this->createMock(PackageManagerAdapterInterface::class),
            $packageCollectionMapper,
        );

        // Act
        $packageResponseDto = $moduleFetcher->require(new ModuleDtoCollection());

        // Arrange
        $this->assertTrue(
            $packageResponseDto->isSuccessful(),
            'Returned PackageManagerResponseDto is successful',
        );

        $this->assertSame(
            ModuleFetcher::MESSAGE_NO_PACKAGES_FOUND,
            $packageResponseDto->getOutputMessage(),
            'Returned PackageManagerResponseDto contains correct message.',
        );

        $this->assertSame(
            0,
            $packageResponseDto->getAppliedPackagesAmount(),
            'Returned PackageManagerResponseDto has 0 in $appliedPackagesAmount.',
        );

        $this->assertCount(
            0,
            $packageResponseDto->getExecutedCommands(),
            'Returned PackageManagerResponseDto has 0 executedCommands.',
        );
    }

    /**
     * @return void
     */
    public function testRequireReturnsFailedResponseDtoIfRequirePackagesFailed(): void
    {
        // Assert
        $packageCollection = new PackageCollection();
        $packageCollection->add(new Package());
        $packageCollectionMapper = $this->createMock(PackageCollectionMapperInterface::class);
        $packageCollectionMapper->expects($this->once())
            ->method('mapModuleCollectionToPackageCollection')
            ->willReturn($packageCollection);

        $packageManager = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManager->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(false, ''));

        $moduleFetcher = new ModuleFetcher(
            $packageManager,
            $packageCollectionMapper,
        );

        // Act
        $packageResponseDto = $moduleFetcher->require(new ModuleDtoCollection());

        // Arrange
        $this->assertFalse(
            $packageResponseDto->isSuccessful(),
            'Returned PackageManagerResponseDto must be failed because require packages operation failed.',
        );
    }

    /**
     * @return void
     */
    public function testRequireReturnsFailedResponseDtoIfItFailedToUpdateSubPackages(): void
    {
        // Assert
        $packageCollection = new PackageCollection();
        $packageCollection->add(new Package());
        $packageCollectionMapper = $this->createMock(PackageCollectionMapperInterface::class);
        $packageCollectionMapper->expects($this->once())
            ->method('mapModuleCollectionToPackageCollection')
            ->willReturn($packageCollection);

        $packageManager = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManager->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(true, ''));
        $packageManager->expects($this->once())
            ->method('updateSubPackage')
            ->willReturn(new PackageManagerResponseDto(false, ''));

        $moduleFetcher = new ModuleFetcher(
            $packageManager,
            $packageCollectionMapper,
        );

        // Act
        $packageResponseDto = $moduleFetcher->require(new ModuleDtoCollection());

        // Arrange
        $this->assertFalse(
            $packageResponseDto->isSuccessful(),
            'Returned PackageManagerResponseDto must be failed because sub-packages update failed.',
        );
    }

    /**
     * @return void
     */
    public function testRequireReturnsFailedResponseDtoIfRequireDevPackagesFailed(): void
    {
        // Assert
        $packageCollection = new PackageCollection();
        $packageCollection->add(new Package());
        $packageCollectionMapper = $this->createMock(PackageCollectionMapperInterface::class);
        $packageCollectionMapper->expects($this->once())
            ->method('mapModuleCollectionToPackageCollection')
            ->willReturn($packageCollection);

        $packageManager = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManager->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(true, ''));
        $packageManager->expects($this->once())
            ->method('updateSubPackage')
            ->willReturn(new PackageManagerResponseDto(true, ''));
        $packageManager->expects($this->once())
            ->method('requireDev')
            ->willReturn(new PackageManagerResponseDto(false, ''));

        $moduleFetcher = new ModuleFetcher(
            $packageManager,
            $packageCollectionMapper,
        );

        // Act
        $packageResponseDto = $moduleFetcher->require(new ModuleDtoCollection());

        // Arrange
        $this->assertFalse(
            $packageResponseDto->isSuccessful(),
            'Returned PackageManagerResponseDto must be failed because require-dev packages operation failed.',
        );
    }
}
