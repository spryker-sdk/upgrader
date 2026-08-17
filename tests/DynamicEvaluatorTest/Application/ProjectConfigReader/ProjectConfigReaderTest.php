<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\ProjectConfigReader;

use DynamicEvaluator\Application\ProjectConfigReader\ConfigReaderInterface;
use DynamicEvaluator\Application\ProjectConfigReader\ProjectConfigReader;
use PHPUnit\Framework\TestCase;
use Upgrader\Configuration\ConfigurationProvider;

class ProjectConfigReaderTest extends TestCase
{
    public function testGetProjectNamespacesShouldReturnProjectNameSpaces(): void
    {
        // Arrange & Assert
        $configurationProviderMock = $this->createConfigurationProviderMock('/data/');
        $configReaderMock = $this->createConfigReaderMock('/data/config/Shared/config_default.php', ['KernelConstants::PROJECT_NAMESPACES']);
        $projectConfigReader = new ProjectConfigReader($configurationProviderMock, $configReaderMock);

        // Act
        $projectConfigReader->getProjectNamespaces();
    }

    protected function createConfigurationProviderMock(string $rootPath): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getRootPath')->willReturn($rootPath);

        return $configurationProvider;
    }

    /**
     * @param array<string> $expectedConfigKeys
     */
    protected function createConfigReaderMock(string $expectedConfigPath, array $expectedConfigKeys): ConfigReaderInterface
    {
        $configReader = $this->createMock(ConfigReaderInterface::class);
        $configReader->expects($this->once())->method('read')->with($expectedConfigPath, $expectedConfigKeys);

        return $configReader;
    }
}
