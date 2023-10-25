<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Validator\Rule;

use Core\Infrastructure\Service\Filesystem;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Exception\UpgraderException;
use Upgrade\Application\Validator\Rule\PhpcsInstallationValidatorRule;
use Upgrader\Configuration\ConfigurationProvider;

class PhpcsInstallationValidationRuleTest extends TestCase
{
    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return \Upgrader\Configuration\ConfigurationProvider
     */
    protected function createConfigurationProviderMock(): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getRootPath')->willReturn('');

        return $configurationProvider;
    }

    /**
     * @param bool $isPhpcsFound
     *
     * @return \Core\Infrastructure\Service\Filesystem
     */
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
