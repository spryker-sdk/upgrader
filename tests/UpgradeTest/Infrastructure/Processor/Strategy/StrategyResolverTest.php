<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeTest\Infrastructure\Processor\Strategy;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\UpgradeStrategyIsNotDefinedException;
use Upgrade\Infrastructure\Processor\Strategy\Composer\ComposerStrategy;
use Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\ReleaseAppStrategy;
use Upgrade\Infrastructure\Processor\Strategy\StrategyResolver;

class StrategyResolverTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testGetStrategyReturnComposerStrategy(): void
    {
        // Arrange
        /** @var \Upgrade\Infrastructure\Processor\Strategy\StrategyResolver $strategyResolver */
        $strategyResolver = static::bootKernel()->getContainer()->get(StrategyResolver::class);

        // Act
        $strategy = $strategyResolver->getStrategy(ConfigurationProvider::COMPOSER_STRATEGY);

        // Assert
        $this->assertInstanceOf(ComposerStrategy::class, $strategy);
    }

    /**
     * @return void
     */
    public function testGetStrategyReturnReleaseAppStrategy(): void
    {
        // Arrange
        /** @var \Upgrade\Infrastructure\Processor\Strategy\StrategyResolver $strategyResolver */
        $strategyResolver = static::bootKernel()->getContainer()->get(StrategyResolver::class);

        // Act
        $strategy = $strategyResolver->getStrategy(ConfigurationProvider::RELEASE_APP_STRATEGY);

        // Assert
        $this->assertInstanceOf(ReleaseAppStrategy::class, $strategy);
    }

    /**
     * @return void
     */
    public function testGetStrategyThrowNotDefinedException(): void
    {
        // Arrange
        /** @var \Upgrade\Infrastructure\Processor\Strategy\StrategyResolver $strategyResolver */
        $strategyResolver = static::bootKernel()->getContainer()->get(StrategyResolver::class);

        // Assert
        $this->expectException(UpgradeStrategyIsNotDefinedException::class);

        // Act
        $strategyResolver->getStrategy('NOT_DEFINED_STRATEGY');
    }
}
