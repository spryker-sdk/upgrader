<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CoreTest\Infrastructure;

use Core\Infrastructure\SemanticVersionHelper;
use PHPUnit\Framework\TestCase;

class SemanticVersionHelperTest extends TestCase
{
    /**
     * @dataProvider getMajorVersionProvider
     *
     * @param mixed $value
     * @param ?int|int $expectedResult
     *
     * @return void
     */
    public function testGetMajorVersion($value, ?int $expectedResult): void
    {
        $this->assertSame($expectedResult, SemanticVersionHelper::getMajorVersion((string)$value));
    }

    /**
     * @return array
     */
    public function getMajorVersionProvider(): array
    {
        return [
            ['', null],
            ['foo-bar-baz', null],
            [555, null],
            [-555, null],
            ['1.0.0', 1],
            ['1.0.2', 1],
            ['1.2.2', 1],
            ['5.2.222', 5],
            ['55.28888.222', 55],
            ['-55.28888.222', null],
        ];
    }
}
