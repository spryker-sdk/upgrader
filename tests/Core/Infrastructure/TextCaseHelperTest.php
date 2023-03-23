<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Core\Infrastructure;

use PHPUnit\Framework\TestCase;

class TextCaseHelperTest extends TestCase
{
    /**
     * @dataProvider camelCaseToDashDataProvider
     *
     * @param string $expectedResult
     * @param string $value
     * @param bool $separateAbbreviation
     *
     * @return void
     */
    public function testCamelCaseToDash(string $expectedResult, string $value, bool $separateAbbreviation): void
    {
        $this->assertSame($expectedResult, TextCaseHelper::camelCaseToDash($value, $separateAbbreviation));
    }

    /**
     * @return array
     */
    public function camelCaseToDashDataProvider(): array
    {
        return [
            ['foo-bar', 'FooBar', true],
            ['foo-bar-baz', 'FooBarBaz', true],
            ['foo-bar-baz', 'FooBarBaz', false],
            ['foo-bar', 'FooBar', false],
            ['foo-bar', 'FOOBar', true],
            ['foobar', 'FOOBar', false],
        ];
    }

    /**
     * @dataProvider dashToCamelCaseDataProvider
     *
     * @param string $expectedResult
     * @param string $value
     * @param bool $upperCaseFirst
     *
     * @return void
     */
    public function testDashToCamelCase(string $expectedResult, string $value, bool $upperCaseFirst): void
    {
        $this->assertSame($expectedResult, TextCaseHelper::dashToCamelCase($value, $upperCaseFirst));
    }

    /**
     * @return array
     */
    public function dashToCamelCaseDataProvider(): array
    {
        return [
            ['FooBar', 'foo-bar', true],
            ['FooBarBaz', 'foo-bar-baz', true],
            ['fooBarBaz', 'foo-bar-baz', false],
            ['fooBar', 'foo-bar', false],
        ];
    }

    /**
     * @dataProvider packageCamelCaseToDashDataProvider
     *
     * @param string $expectedResult
     * @param string $originName
     *
     * @return void
     */
    public function testPackageCamelCaseToDash(string $expectedResult, string $originName): void
    {
        $this->assertSame($expectedResult, TextCaseHelper::packageCamelCaseToDash($originName));
    }

    /**
     * @return array
     */
    public function packageCamelCaseToDashDataProvider(): array
    {
        return [
            ['spryker/symfony-mailer', 'Spryker.SymfonyMailer'],
            ['my-company/my-package', 'MyCompany.MyPackage'],
        ];
    }
}
