<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\PackageManager\CommandExecutor;

use Core\Infrastructure\Service\ProcessRunnerService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Process\Process;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerCommandExecutor;

class ComposerCommandExecutorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerCommandExecutor
     */
    protected ComposerCommandExecutor $cmdExecutor;

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider|\Upgrade\Application\Provider\ConfigurationProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected ConfigurationProvider $mockConfigurationProvider;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $processRunner = $this->prophesize(ProcessRunnerService::class);

        $processRunner->run(Argument::type('array'), Argument::type('array'))->will(function ($args) {
            return new Process($args[0], '');
        });
        $this->mockConfigurationProvider = $this->mockConfigurationProvider();

        $this->cmdExecutor = new ComposerCommandExecutor($processRunner->reveal(), $this->mockConfigurationProvider);
    }

    /**
     * @return void
     */
    public function testRequireWithDependencies(): void
    {
        $packageCollection = new PackageCollection([
            new Package('spryker-sdk/sdk-contracts', '0.2.1', '0.2.0'),
        ]);
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(false);

        $response = $this->cmdExecutor->require($packageCollection);

        $this->assertSame('composer require spryker-sdk/sdk-contracts:0.2.1 --no-scripts --no-plugins --with-all-dependencies', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testRequire(): void
    {
        $packageCollection = new PackageCollection([
            new Package('spryker-sdk/sdk-contracts', '0.2.1', '0.2.0'),
        ]);
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(true);

        $response = $this->cmdExecutor->require($packageCollection);

        $this->assertSame('composer require spryker-sdk/sdk-contracts:0.2.1 --no-scripts --no-plugins --with-all-dependencies --no-install', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testRemove(): void
    {
        $packageCollection = new PackageCollection([
            new Package('spryker-sdk/sdk-contracts'),
        ]);
        $response = $this->cmdExecutor->remove($packageCollection);

        $this->assertSame('composer remove spryker-sdk/sdk-contracts --no-scripts --no-plugins', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testRequireDev(): void
    {
        $packageCollection = new PackageCollection([
            new Package('phpspec/prophecy-phpunit', '2.0.1', '2.0.0'),
        ]);
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(true);
        $response = $this->cmdExecutor->requireDev($packageCollection);

        $this->assertSame('composer require phpspec/prophecy-phpunit:2.0.1 --no-scripts --no-plugins --with-all-dependencies --dev --no-install', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testUpdateSubPackage(): void
    {
        $packageCollection = new PackageCollection([
            new Package('phpspec/prophecy-phpunit', '2.0.1', '2.0.0'),
        ]);
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(true);
        $response = $this->cmdExecutor->updateSubPackage($packageCollection);

        $this->assertSame('composer update phpspec/prophecy-phpunit:2.0.1 --no-install', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(true);
        $response = $this->cmdExecutor->update();

        $this->assertSame('composer update --with-all-dependencies --no-scripts --no-plugins --no-interaction --no-install', $response->getOutputMessage());
    }

    /**
     * @return \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected function mockConfigurationProvider(): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);

        return $configurationProvider;
    }
}
