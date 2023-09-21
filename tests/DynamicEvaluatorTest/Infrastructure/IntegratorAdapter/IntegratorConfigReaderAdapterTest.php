<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Infrastructure\IntegratorAdapter;

use DynamicEvaluator\Infrastructure\IntegratorAdapter\IntegratorConfigReaderAdapter;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\ConfigReader\ConfigReaderInterface as IntegratorConfigReaderInterface;

class IntegratorConfigReaderAdapterTest extends TestCase
{
    /**
     * @return void
     */
    public function testReadShouldReturnIntegratorReaderResult(): void
    {
        // Arrange
        $key = 'KernelConstants::PROJECT_NAMESPACES';
        $expectedResult = [$key => 'val'];
        $configPath = '/data/config/Shared/config_default.php';
        $configKeys = [$key];

        $integratorConfigReaderMock = $this->createIntegratorConfigReaderMock($expectedResult, $configPath, $configKeys);
        $integratorConfigReaderAdapter = new IntegratorConfigReaderAdapter($integratorConfigReaderMock);

        // Act
        $result = $integratorConfigReaderAdapter->read($configPath, $configKeys);

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @param array $expectedReaderResult
     * @param string $expectedConfigPath
     * @param array<mixed> $expectedConfigKeys
     *
     * @return \SprykerSdk\Integrator\ConfigReader\ConfigReaderInterface
     */
    protected function createIntegratorConfigReaderMock(
        array $expectedReaderResult,
        string $expectedConfigPath,
        array $expectedConfigKeys
    ): IntegratorConfigReaderInterface {
        $integratorConfigReader = $this->createMock(IntegratorConfigReaderInterface::class);
        $integratorConfigReader->expects($this->once())->method('read')->with(
            $expectedConfigPath,
            $expectedConfigKeys,
        )->willReturn($expectedReaderResult);

        return $integratorConfigReader;
    }
}
