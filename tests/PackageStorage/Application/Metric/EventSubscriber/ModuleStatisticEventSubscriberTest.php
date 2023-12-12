<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Metric\EventSubscriber;

use PackageStorage\Application\Fetcher\ProjectExtendedClassesFetcherInterface;
use PackageStorage\Application\Fetcher\VendorChangedClassesFetcherInterface;
use PackageStorage\Application\Metric\ModuleStatisticUpdater;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\ModelStatisticDto;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class ModuleStatisticEventSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPostRequireShouldAddViolationsIntoResponseDto(): void
    {
        // Arrange
        $vendorChangedClassesFetcherMock = $this->createMock(VendorChangedClassesFetcherInterface::class);
        $vendorChangedClassesFetcherMock->expects($this->once())->method('fetchVendorChangedClassesWithPackage')
            ->willReturn([
                'package2' => 'package2',
                'package4' => 'package4',
            ]);
        $projectExtendedClassesFetcherMock = $this->createMock(ProjectExtendedClassesFetcherInterface::class);
        $projectExtendedClassesFetcherMock->expects($this->exactly(2))->method('fetchExtendedClasses')
            ->willReturn([
                'package1' => 'package1',
                'package2' => 'package2',
                'package3' => 'package3',
            ]);
        $checkerExecutorEventSubscriber = new ModelStatisticEventSubscriber(new ModuleStatisticUpdater($projectExtendedClassesFetcherMock, $vendorChangedClassesFetcherMock));

        $stepsResponseDto = new StepsResponseDto();
        $preEvent = new ReleaseGroupProcessorEvent($stepsResponseDto);
        $postEvent = new ReleaseGroupProcessorPostRequireEvent($stepsResponseDto, new PackageManagerResponseDto(true));

        // Act
        $checkerExecutorEventSubscriber->onPreRequire($preEvent);
        $checkerExecutorEventSubscriber->onPostRequire($postEvent);

        // Assert
        $modelStatisticDto = $stepsResponseDto->getModelStatisticDto();
        $this->assertEquals(new ModelStatisticDto(3, 2, 1), $modelStatisticDto);
    }
}
