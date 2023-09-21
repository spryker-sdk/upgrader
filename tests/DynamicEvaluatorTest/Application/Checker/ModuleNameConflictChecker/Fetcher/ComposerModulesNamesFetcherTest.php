<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\ModuleNameConflictChecker\Fetcher;

use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ComposerModulesNamesFetcher;
use PHPUnit\Framework\TestCase;
use Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReader;

class ComposerModulesNamesFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchComposerModulesShouldReturnSprykerModuleNames(): void
    {
        // Arrange
        $composerLockData = [
            'packages' => [
                [
                    'name' => 'aws/aws-crt-php',
                    'version' => 'v1.2.1',
                ],
                [
                    'name' => 'spryker-shop/calculation-page',
                    'version' => '1.3.2',
                ],
                [
                    'name' => 'spryker/acl',
                    'version' => '3.17.0',
                ],
            ],
            'packages-dev' => [
                [
                    'name' => 'spryker-shop/web-profiler-widget',
                    'version' => '1.4.2',
                ],
                [
                    'name' => 'sebastian/version',
                    'version' => '3.0.2',
                ],
            ],
        ];

        $composerLockReaderMock = $this->createComposerLockReaderMock($composerLockData);
        $composerModulesNamesFetcher = new ComposerModulesNamesFetcher($composerLockReaderMock);

        // Act
        $modulesNames = $composerModulesNamesFetcher->fetchComposerModules();

        // Assert
        $this->assertSame(['CalculationPage', 'Acl', 'WebProfilerWidget'], $modulesNames);
    }

    /**
     * @param array<string, mixed> $composerLockData
     *
     * @return \Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReader
     */
    protected function createComposerLockReaderMock(array $composerLockData): ComposerLockReader
    {
        $composerLockReader = $this->createMock(ComposerLockReader::class);
        $composerLockReader->method('read')->willReturn($composerLockData);

        return $composerLockReader;
    }
}
