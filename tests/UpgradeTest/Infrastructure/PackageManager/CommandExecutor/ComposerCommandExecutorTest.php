<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeTest\Infrastructure\PackageManager\CommandExecutor;

use Core\Infrastructure\Service\ProcessRunnerService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Process\Process;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerCommandExecutor;

class ComposerCommandExecutorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerCommandExecutor
     */
    protected ComposerCommandExecutor $cmdExecutor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $processRunner = $this->prophesize(ProcessRunnerService::class);

        $processRunner->run(Argument::type('array'))->will(function ($args) {
            return new Process($args[0], '');
        });

        $this->cmdExecutor = new ComposerCommandExecutor($processRunner->reveal());
    }

    /**
     * @return void
     */
    public function testRequire(): void
    {
        $packageCollection = new PackageCollection([
            new Package('spryker-sdk/sdk-contracts', '0.2.1', '0.2.0'),
        ]);
        $response = $this->cmdExecutor->require($packageCollection);

        $this->assertSame('composer require spryker-sdk/sdk-contracts:0.2.1 --no-scripts --no-plugins --with-all-dependencies', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testRequireDev(): void
    {
        $packageCollection = new PackageCollection([
            new Package('phpspec/prophecy-phpunit', '2.0.1', '2.0.0'),
        ]);
        $response = $this->cmdExecutor->requireDev($packageCollection);

        $this->assertSame('composer require phpspec/prophecy-phpunit:2.0.1 --no-scripts --no-plugins --with-all-dependencies --dev', $response->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        $response = $this->cmdExecutor->update();

        $this->assertSame('composer update --with-all-dependencies --no-scripts --no-plugins --no-interaction', $response->getOutputMessage());
    }
}
