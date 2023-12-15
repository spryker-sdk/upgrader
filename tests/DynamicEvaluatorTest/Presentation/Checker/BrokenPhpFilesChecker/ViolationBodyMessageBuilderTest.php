<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Presentation\Checker\BrokenPhpFilesChecker;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto;
use DynamicEvaluator\Presentation\Checker\BrokenPhpFilesChecker\ViolationBodyMessageBuilder;
use PHPUnit\Framework\TestCase;
use Upgrader\Configuration\ConfigurationProvider;

class ViolationBodyMessageBuilderTest extends TestCase
{
    /**
     * @return void
     */
    public function testBuildViolationsMessage(): void
    {
        $violations = [
            new ViolationDto(
                ['composer update spryker/customer:7.53.0 spryker/security-gui:1.5.0 spryker/user-password-reset:1.5.0 spryker-shop/customer-page:2.49.0 --no-scripts --no-plugins --no-interaction -w'],
                [
                    new FileErrorDto(
                        'src/Pyz/Service/Shipment/ShipmentConfig.php',
                        0,
                        'Access to undefined constant Generated\Shared\Transfer\ShipmentTransfer::SHIPMENT_TYPE_UUID.',
                    ),
                ]
            ),
            new ViolationDto(
                [],
                [
                    new FileErrorDto(
                        'src/Pyz/Zed/DataImport/Business/Model/Product/Repository/ProductRepository.php',
                        0,
                        'Call to an undefined method Propel\Runtime\Collection\Collection::toArray().',
                    ),
                ],
            ),
        ];

        $builder = new ViolationBodyMessageBuilder(new ConfigurationProvider());
        $message = $builder->buildViolationsMessage($violations, []);

        $this->assertSame('<details><summary><h4>PHP classes that became not compatible with Spryker Release</h4></summary>Switch to this branch, bootstrap your project in the development environment, open the mentioned file, and compare its correctness to the released version by Spryker.

| Composer command | Project file(s) |
|------------------|-----------------|
| composer update spryker/customer:7.53.0 spryker/security-gui:1.5.0 spryker/user-password-reset:1.5.0 spryker-shop/customer-page:2.49.0 --no-scripts --no-plugins --no-interaction -w | <b>src/Pyz/Service/Shipment/ShipmentConfig.php</b><br>Access to undefined constant Generated\Shared\Transfer\ShipmentTransfer::SHIPMENT_TYPE_UUID.<br> |
| - | <b>src/Pyz/Zed/DataImport/Business/Model/Product/Repository/ProductRepository.php</b><br>Call to an undefined method Propel\Runtime\Collection\Collection::toArray().<br> |

</details>', $message);
    }
}
