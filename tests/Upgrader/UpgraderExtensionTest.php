<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UpgraderExtensionTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateExtension(): void
    {
        // Arrange
        $containerBuilder = new ContainerBuilder();
        $configs = [];

        // Act
        (new UpgraderExtension())->load($configs, $containerBuilder);

        // Assert
        $this->assertNotEmpty($containerBuilder->getResources());
    }
}
