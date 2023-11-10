<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Factory;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Factory\ComposerViolationDtoFactory;

class ComposerViolationDtoFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateFromPackageManagerResponseShouldCreateViolationResponse(): void
    {
        // Arrange
        $packageManagerResponseDto = new PackageManagerResponseDto(
            false,
            <<<OUT
            Some text
            Some text
            Some text
              Problem 1
                - Root composer.json requires spryker/country-gui 1.0.0 -> satisfiable by spryker/country-gui[1.0.0].
                - spryker/country-gui 1.0.0 requires spryker/country ^4.0.0 -> found spryker/country[dev-master, 4.0.0, 4.0.x-dev, 4.1.0] but these were not loaded, likely because it conflicts with another require.
              Problem 2
                - Root composer.json requires spryker/currency-gui 1.0.0 -> satisfiable by spryker/currency-gui[1.0.0].
                - spryker/currency-gui 1.0.0 requires spryker/currency ^4.0.0 -> found spryker/currency[dev-master, 4.0.0, 4.0.x-dev] but these were not loaded, likely because it conflicts with another require.
              Problem 3
                - Root composer.json requires spryker/locale-gui 1.0.0 -> satisfiable by spryker/locale-gui[1.0.0].
                - spryker/locale-gui 1.0.0 requires spryker/locale ^4.0.0 -> found spryker/locale[dev-master, 4.0.0, ..., 4.2.0] but these were not loaded, likely because it conflicts with another require.
            OUT,
        );

        $factory = new ComposerViolationDtoFactory();

        // Act
        $violationDto = $factory->createFromPackageManagerResponse($packageManagerResponseDto);

        // Assert
        $this->assertSame(
            <<<OUT
          Problem 1
              - Root composer.json requires spryker/country-gui 1.0.0 -> satisfiable by spryker/country-gui[1.0.0].
              - spryker/country-gui 1.0.0 requires spryker/country ^4.0.0 -> found spryker/country[dev-master, 4.0.0, 4.0.x-dev, 4.1.0] but these were not loaded, likely because it conflicts with another require.
            Problem 2
              - Root composer.json requires spryker/currency-gui 1.0.0 -> satisfiable by spryker/currency-gui[1.0.0].
              - spryker/currency-gui 1.0.0 requires spryker/currency ^4.0.0 -> found spryker/currency[dev-master, 4.0.0, 4.0.x-dev] but these were not loaded, likely because it conflicts with another require.
            Problem 3
              - Root composer.json requires spryker/locale-gui 1.0.0 -> satisfiable by spryker/locale-gui[1.0.0].
              - spryker/locale-gui 1.0.0 requires spryker/locale ^4.0.0 -> found spryker/locale[dev-master, 4.0.0, ..., 4.2.0] but these were not loaded, likely because it conflicts with another require.
          OUT,
            $violationDto->getMessage(),
        );
    }
}
