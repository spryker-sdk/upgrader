<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Processor;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Upgrade\Application\Exception\ReleaseGroupRequireProcessorIsNotDefinedException;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorResolver;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class ReleaseGroupProcessorResolverTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testGetProcessorSuccessful(): void
    {
        // Arrange
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $configurationProviderMock
            ->method('getReleaseGroupProcessor')
            ->willReturn('test');
        $processorMock = $this->createMock(ReleaseGroupProcessorInterface::class);
        $processorMock
            ->method('getProcessorName')
            ->willReturn('test');

        $releaseGroupProcessorResolver = new ReleaseGroupProcessorResolver(
            $configurationProviderMock,
            [$processorMock],
        );

        // Act
        $processor = $releaseGroupProcessorResolver->getProcessor();

        // Assert
        $this->assertSame(
            $processorMock,
            $processor,
        );
    }

    /**
     * @return void
     */
    public function testGetProcessorUnsuccessful(): void
    {
        // Arrange
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $configurationProviderMock
            ->method('getReleaseGroupProcessor')
            ->willReturn('not_exist');
        $processor1Mock = $this->createMock(ReleaseGroupProcessorInterface::class);
        $processor1Mock
            ->method('getProcessorName')
            ->willReturn('test1');
        $processor2Mock = $this->createMock(ReleaseGroupProcessorInterface::class);
        $processor2Mock
            ->method('getProcessorName')
            ->willReturn('test2');

        $releaseGroupProcessorResolver = new ReleaseGroupProcessorResolver(
            $configurationProviderMock,
            [$processor1Mock, $processor2Mock],
        );

        // Assert
        $this->expectException(ReleaseGroupRequireProcessorIsNotDefinedException::class);
        $this->expectExceptionMessage('`not_exist` processor is not available. Available processors: test1,test2.');

        // Act
        $releaseGroupProcessorResolver->getProcessor();
    }
}
