<?php

declare(strict_types=1);

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
use Upgrade\Domain\Entity\Package;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerLockComparatorCommandExecutor;

class ComposerLockComparatorCommandExecutorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerLockComparatorCommandExecutor
     */
    protected ComposerLockComparatorCommandExecutor $cmdExecutor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $processRunner = $this->prophesize(ProcessRunnerService::class);

        $process = $this->prophesize(Process::class);

        $diffData = [
            'changes' => [
                'spryker-sdk/sdk-contracts' => [
                    '0.2.0', '0.2.1', 'diff-link-here',
                ],
            ],
            'changes-dev' => [
                'phpspec/prophecy-phpunit' => [
                    '2.0.0', '2.0.1', 'diff-link-here',
                ],
            ],
        ];

        $processRunner->run(Argument::type('array'))->will(function ($args) use ($process, $diffData) {
            $process->setInput(Argument::any())->willReturn($process);
            $process->willBeConstructedWith([$args[0], '']);
            $process->isStarted()->willReturn(true);
            $process->getOutput()->willReturn(json_encode($diffData));

            return $process->reveal();
        });

        $this->cmdExecutor = new ComposerLockComparatorCommandExecutor($processRunner->reveal());
    }

    /**
     * @return void
     */
    public function testGetComposerLockDiff(): void
    {
        $response = $this->cmdExecutor->getComposerLockDiff();

        $this->assertEquals(
            [
                new Package('spryker-sdk/sdk-contracts', '0.2.1', '0.2.0', 'diff-link-here'),
            ],
            $response->getRequireChanges(),
        );

        $this->assertEquals(
            [
                new Package('phpspec/prophecy-phpunit', '2.0.1', '2.0.0', 'diff-link-here'),
            ],
            $response->getRequireDevChanges(),
        );
    }
}
