<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Validator\Rule;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Utils\Infrastructure\Service\Filesystem;
use Upgrade\Application\Exception\UpgraderException;
use Upgrade\Application\Validator\Rule\PhpcsInstallationValidatorRule;
use Upgrader\Configuration\ConfigurationProvider;

class PhpcsInstallationValidatorRuleTest extends TestCase
{
    public function testValidateShouldThrowExceptionWhenPhpcsNotFound(): void
    {
        // Arrange & Assert
        $this->expectException(UpgraderException::class);

        $validator = new PhpcsInstallationValidatorRule(
            $this->createConfigurationProviderMock(),
            $this->createFilesystemMock(false),
        );

        // Act
        $validator->validate();
    }

    public function testValidateShouldNotThrowExceptionWhenFound(): void
    {
        // Arrange & Assert
        $validator = new PhpcsInstallationValidatorRule(
            $this->createConfigurationProviderMock(),
            $this->createFilesystemMock(),
        );

    // Act
        $validator->validate();
    }

    public function testGetViolationTitleShouldReturnTitle(): void
    {
        // Arrange
        $validator = new PhpcsInstallationValidatorRule(
            $this->createMock(ConfigurationProvider::class),
            $this->createMock(Filesystem::class),
        );

        // Act
        $title = $validator->getViolationTitle();

        // Assert
        $this->assertSame(PhpcsInstallationValidatorRule::VIOLATION_TITLE, $title);
    }

    protected function createConfigurationProviderMock(): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getRootPath')->willReturn('');

        return $configurationProvider;
    }

    protected function createFilesystemMock(bool $isPhpcsFound = true): Filesystem
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
        ->method('exists')
        ->with(PhpcsInstallationValidatorRule::PHP_CS_FIXER_PATH)
        ->willReturn($isPhpcsFound);

        return $filesystem;
    }
}
