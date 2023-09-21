<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Infrastructure\IntegratorAdapter;

use DynamicEvaluator\Infrastructure\IntegratorAdapter\IntegratorConfigReaderFactory;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\ConfigReader\ConfigReaderInterface;

class IntegratorConfigReaderFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateConfigReaderShouldReturnIntegratorConfigReader(): void
    {
        // Arrange
        $integratorConfigReaderFactory = new IntegratorConfigReaderFactory();

        // Act
        $configReader = $integratorConfigReaderFactory->createConfigReader();

        // Assert
        $this->assertInstanceOf(ConfigReaderInterface::class, $configReader);
    }
}
