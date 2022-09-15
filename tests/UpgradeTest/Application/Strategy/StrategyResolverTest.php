<?php
declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeTest\Application\Strategy;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Upgrade\Application\Exception\UpgradeStrategyIsNotDefinedException;
use Upgrade\Application\Strategy\Composer\ComposerStrategy;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseAppStrategy;
use Upgrade\Application\Strategy\StrategyResolver;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class StrategyResolverTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testGetStrategyReturnComposerStrategy(): void
    {
        // Arrange
        /** @var \Upgrade\Application\Strategy\StrategyResolver $strategyResolver */
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
        /** @var \Upgrade\Application\Strategy\StrategyResolver $strategyResolver */
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
        /** @var \Upgrade\Application\Strategy\StrategyResolver $strategyResolver */
        $strategyResolver = static::bootKernel()->getContainer()->get(StrategyResolver::class);

        // Assert
        $this->expectException(UpgradeStrategyIsNotDefinedException::class);

        // Act
        $strategyResolver->getStrategy('NOT_DEFINED_STRATEGY');
    }
}
