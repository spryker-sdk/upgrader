<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\IO;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Upgrade\Infrastructure\IO\FinderFactory;

class FinderFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateFinderShouldCreateFinder(): void
    {
        // Arrange
        $finderFactory = new FinderFactory();

        // Act
        $finder = $finderFactory->createFinder();

        // Assert
        $this->assertInstanceOf(Finder::class, $finder);
    }
}
