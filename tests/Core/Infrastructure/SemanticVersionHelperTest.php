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
     * @param ?int|null $expectedResult
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
        $validSemanticVersions = [
            ['1.0.0', 1],
            ['0.1.0', 0],
            ['0.0.1', 0],
            ['10.20.30', 10],
            ['2.3.4-beta', 2],
            ['5.6.7-alpha.123', 5],
            ['1.2.3-rc.456+build789', 1],
            ['3.2.1+build123', 3],
            ['4.5.6-beta.789+build999', 4],
            ['1.2', 1],
            ['1.2.a', 1],
            ['1.2.3.4', 1],
            ['1.2.3-rc!', 1],
            ['1.2.3-beta@456', 1],
            ['1.2.3+build_789', 1],
            ['1.2.3-rc.456+build_789', 1],
            ['1.2.3-beta+build!999', 1],
            ['1.2.3-beta+build.999-456', 1],
        ];

        $invalidSemanticVersions = [
            ['', null],
            ['foo-bar-baz', null],
            ['-55.28888.222', null],
        ];

        return [...$validSemanticVersions, ...$invalidSemanticVersions];
    }
}
