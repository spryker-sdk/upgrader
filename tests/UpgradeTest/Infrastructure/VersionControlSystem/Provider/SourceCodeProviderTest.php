<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeTest\Infrastructure\VersionControlSystem\Provider;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\SourceCodeProviderIsNotDefinedException;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub\GitHubSourceCodeSourceCodeProvider;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProvider;

class SourceCodeProviderTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testGetSourceCodeProviderReturnDefaultProvider(): void
    {
        // Arrange
        /** @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProvider $strategyResolver */
        $strategyResolver = static::bootKernel()->getContainer()->get(SourceCodeProvider::class);

        // Act
        $sourceCodeProvider = $strategyResolver->getSourceCodeProvider();

        // Assert
        $this->assertInstanceOf(GitHubSourceCodeSourceCodeProvider::class, $sourceCodeProvider);
    }

    /**
     * @return void
     */
    public function testGetSourceCodeProviderThrowNotDefinedException(): void
    {
        // Arrange
        $container = static::bootKernel()->getContainer();

        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getSourceCodeProvider')->willReturn('NOT_DEFINED_PROVIDER');
        $container->set('configuration.provider', $configurationProvider);

        /** @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProvider $strategyResolver */
        $strategyResolver = $container->get(SourceCodeProvider::class);

        // Assert
        $this->expectException(SourceCodeProviderIsNotDefinedException::class);

        // Act
        $strategyResolver->getSourceCodeProvider();
    }
}
