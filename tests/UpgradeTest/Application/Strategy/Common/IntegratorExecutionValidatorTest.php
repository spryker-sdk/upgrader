<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\Common\IntegratorExecutionValidator;

class IntegratorExecutionValidatorTest extends TestCase
{
    /**
     * @dataProvider configurationDataProvider
     *
     * @param bool $isIntegratorEnabled
     * @param int $getManifestsRatingThreshold
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testIsIntegratorShouldBeInvokedShouldReturnValidState(
        bool $isIntegratorEnabled,
        int $getManifestsRatingThreshold,
        bool $expectedResult
    ): void {
        // Arrange
        $configurationProviderMock = $this->createMock(ConfigurationProviderInterface::class);
        $configurationProviderMock->method('isIntegratorEnabled')->willReturn($isIntegratorEnabled);
        $configurationProviderMock->method('getManifestsRatingThreshold')->willReturn($getManifestsRatingThreshold);
        $integratorEvaluator = new IntegratorExecutionValidator($configurationProviderMock);

        // Act
        $result = $integratorEvaluator->isIntegratorShouldBeInvoked();

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array<mixed>
     */
    public function configurationDataProvider(): array
    {
        return [
            [false, 50, false],
            [false, 101, false],
            [true, 101, false],
            [true, 100, true],
        ];
    }
}
