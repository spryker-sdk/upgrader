<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\EnvParser;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Upgrade\Infrastructure\EnvParser\EnvFetcher;

class EnvFetcherTest extends TestCase
{
    /**
     * @dataProvider getBoolValueDataProvider
     *
     * @param string|null $varValue
     * @param bool|null $expectedVarValue
     * @param bool|null $defaultValue
     *
     * @return void
     */
    public function testGetBoolShouldReturnProperValue(
        ?string $varValue,
        ?bool $expectedVarValue,
        ?bool $defaultValue = null
    ): void {
        // Arrange && Assert
        if ($varValue !== null) {
            putenv(sprintf('SOME_VAR=%s', $varValue));
        }

        // Act
        $value = EnvFetcher::getBool('SOME_VAR', $defaultValue);

        // Assert
        $this->assertSame($expectedVarValue, $value);
    }

    /**
     * @dataProvider getBoolValueDataExceptionProvider
     *
     * @param string|null $varValue
     * @param bool|null $expectedVarValue
     * @param bool|null $defaultValue
     *
     * @return void
     */
    public function testGetBoolShouldReturnProperValueShouldThrowException(
        ?string $varValue,
        ?bool $expectedVarValue,
        ?bool $defaultValue = null
    ): void {
        // Arrange && Assert
        if ($varValue !== null) {
            putenv(sprintf('SOME_VAR=%s', $varValue));
        }

        $this->expectException(InvalidArgumentException::class);

        // Act
        $value = EnvFetcher::getBool('SOME_VAR', $defaultValue);
    }

    /**
     * @return array<mixed>
     */
    public function getBoolValueDataProvider(): array
    {
        return [
            ['1', true],
            ['true', true],
            ['0', false],
            ['false', false],
            ['fake', true, true],
            ['fake', false, false],
            [null, true, true],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getBoolValueDataExceptionProvider(): array
    {
        return [
            ['fake', null, null, true],
            [null, null, null, true],
        ];
    }
}
