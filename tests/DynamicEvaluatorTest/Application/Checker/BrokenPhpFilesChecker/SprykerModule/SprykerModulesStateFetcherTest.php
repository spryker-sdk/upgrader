<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\BrokenPhpFilesChecker\SprykerModule;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateFetcher;
use PHPUnit\Framework\TestCase;
use Upgrade\Infrastructure\PackageManager\Reader\ComposerReaderInterface;

class SprykerModulesStateFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchCurrentSprykerModulesStateShouldReturnValidPackages(): void
    {
        // Arrange
        $composerReaderMock = $this->createComposerReaderMock([
            'packages' => [
                [
                    'name' => 'doctrine/deprecations',
                    'version' => 'v1.0.0',
                ],
                [
                    'name' => 'spryker/acl',
                    'version' => '3.18.0',
                ],
                [
                    'name' => 'spryker-shop/shop-ui',
                    'version' => '1.73.0',
                ],
                [
                    'name' => 'spryker-feature/warehouse-picking',
                    'version' => '202311.0',
                ],
            ],
            'minimum-stability' => 'dev',
        ]);

        $sprykerModulesStateFetcher = new SprykerModulesStateFetcher($composerReaderMock);

        // Act
        $modules = $sprykerModulesStateFetcher->fetchCurrentSprykerModulesState();

        // Assert
        $this->assertSame(['spryker/acl' => '3.18.0', 'spryker-shop/shop-ui' => '1.73.0'], $modules);
    }

    /**
     * @param array<string> $composerLockData
     *
     * @return \Upgrade\Infrastructure\PackageManager\Reader\ComposerReaderInterface
     */
    protected function createComposerReaderMock(array $composerLockData): ComposerReaderInterface
    {
        $composerReader = $this->createMock(ComposerReaderInterface::class);
        $composerReader->method('read')->willReturn($composerLockData);

        return $composerReader;
    }
}
