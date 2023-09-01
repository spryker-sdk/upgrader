<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\PackageManager\CommandExecutor;

use Core\Infrastructure\Service\ProcessRunnerService;
use Generator;
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
     * @var \Core\Infrastructure\Service\ProcessRunnerService|\Prophecy\Prophecy\ObjectProphecy
     */
    protected $processRunner;

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
        $this->processRunner = $this->prophesize(ProcessRunnerService::class);
        $this->mockConfigurationProvider = $this->mockConfigurationProvider();

        $this->cmdExecutor = new ComposerCommandExecutor($this->processRunner->reveal(), $this->mockConfigurationProvider);
    }

    /**
     * @return void
     */
    public function testRequireWithDependencies(): void
    {
        $this->runWithoutStrategy();
        $packageCollection = new PackageCollection([
            new Package('spryker-sdk/sdk-contracts', '0.2.1', '0.2.0'),
        ]);
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(false);

        $response = $this->cmdExecutor->require($packageCollection);

        $this->assertSame('composer require spryker-sdk/sdk-contracts:0.2.1 --no-scripts --no-plugins -W', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testRequire(): void
    {
        $this->runWithoutStrategy();
        $packageCollection = new PackageCollection([
            new Package('spryker-sdk/sdk-contracts', '0.2.1', '0.2.0'),
        ]);
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(true);

        $response = $this->cmdExecutor->require($packageCollection);

        $this->assertSame('composer require spryker-sdk/sdk-contracts:0.2.1 --no-scripts --no-plugins -W --no-install', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testRemove(): void
    {
        $this->runWithoutStrategy();
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
        $this->runWithoutStrategy();
        $packageCollection = new PackageCollection([
            new Package('phpspec/prophecy-phpunit', '2.0.1', '2.0.0'),
        ]);
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(true);
        $response = $this->cmdExecutor->requireDev($packageCollection);

        $this->assertSame('composer require phpspec/prophecy-phpunit:2.0.1 --no-scripts --no-plugins --dev -W --no-install', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testUpdateSubPackage(): void
    {
        $this->runWithoutStrategy();
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
        $this->runWithoutStrategy();
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(true);
        $response = $this->cmdExecutor->update();

        $this->assertSame('composer update --no-scripts --no-plugins --no-interaction -W --no-install', $response->getOutputMessage());
    }

    /**
     * @group test
     *
     * @dataProvider getStrategyCallDataProvider
     *
     * @param array $isSuccessful
     * @param array $arguments
     *
     * @return void
     */
    public function testUpdateWithStrategy(array $isSuccessful, array $arguments): void
    {
        $processMock = $this->createMock(Process::class);
        $processMock->method('getCommandLine')->willReturn('');
        $processMock->expects($this->exactly(count($isSuccessful)))->method('isSuccessful')->willReturnOnConsecutiveCalls(...$isSuccessful);
        $processRunnerServiceMock = $this->createMock(ProcessRunnerService::class);
        $processRunnerServiceMock
            ->expects($this->exactly(count($arguments)))
            ->method('run')
            ->withConsecutive(...$arguments)
            ->willReturn($processMock);
        $this->mockConfigurationProvider->method('getComposerNoInstall')
            ->willReturn(true);

        $cmdExecutor = new ComposerCommandExecutor($processRunnerServiceMock, $this->mockConfigurationProvider, true);
        $response = $cmdExecutor->update();
    }

    /**
     * @return \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected function mockConfigurationProvider(): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);

        return $configurationProvider;
    }

    /**
     * @return void
     */
    protected function runWithoutStrategy(): void
    {
        $this->processRunner->run(Argument::type('array'), Argument::type('array'))->shouldBeCalledOnce()
            ->will(function ($args) {
                return new Process($args[0], '');
            });
    }

    /**
     * @return \Generator
     */
    public function getStrategyCallDataProvider(): Generator
    {
        $dataProvider = [
            [
                [true, true],
                [
                    [['composer', 'update', '--no-scripts', '--no-plugins', '--no-interaction', '--no-install'], ComposerCommandExecutor::ENV],
                ],
            ],
            [
                [false, true, true],
                [
                    [['composer', 'update', '--no-scripts', '--no-plugins', '--no-interaction', '--no-install'], ComposerCommandExecutor::ENV],
                    [['composer', 'update', '--no-scripts', '--no-plugins', '--no-interaction', '-w', '--no-install'], ComposerCommandExecutor::ENV],
                ],
            ],
            [
                [false, false, true, true],
                [
                    [['composer', 'update', '--no-scripts', '--no-plugins', '--no-interaction', '--no-install'], ComposerCommandExecutor::ENV],
                    [['composer', 'update', '--no-scripts', '--no-plugins', '--no-interaction', '-w', '--no-install'], ComposerCommandExecutor::ENV],
                    [['composer', 'update', '--no-scripts', '--no-plugins', '--no-interaction', '-W', '--no-install'], ComposerCommandExecutor::ENV],
                ],
            ],
        ];

        foreach ($dataProvider as $set) {
            yield $set;
        }
    }
}
