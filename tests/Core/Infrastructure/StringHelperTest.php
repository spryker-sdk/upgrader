<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CoreTest\Infrastructure;

use Core\Infrastructure\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    /**
     * @return void
     */
    public function testFromDashToCamelCaseShouldReturnValidResult(): void
    {
        // Act
        $resultOne = StringHelper::fromDashToCamelCase('some-dash-value');
        $resultTwo = StringHelper::fromDashToCamelCase('some-dash-value', false);

        // Assert
        $this->assertSame('SomeDashValue', $resultOne);
        $this->assertSame('someDashValue', $resultTwo);
    }
}
