<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgraderTest\Commands\Upgrade;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Service\UpgradeServiceInterface;
use Upgrader\Console\UpgraderConsole;

class UpgradeCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteSuccessfulUpgrade(): void
    {
        // Create a mock of the UpgradeServiceInterface
        $upgradeService = $this->createMock(UpgradeServiceInterface::class);

        // Configure the mock to return a successful result
        $upgradeService->expects($this->once())
            ->method('upgrade')
            ->willReturn($this->createSuccessfulExecutionDto());

        // Create a mock for InputInterface and OutputInterface
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // Create an instance of the UpgraderConsole and inject the mock UpgradeServiceInterface
        $upgraderConsole = new UpgraderConsole($upgradeService);

        // Call the execute method
        $result = $upgraderConsole->execute($input, $output);

        // Assert that the result is a success
        $this->assertSame(UpgraderConsole::SUCCESS, $result);
    }

    /**
     * @return void
     */
    public function testExecuteFailedUpgrade(): void
    {
        // Create a mock of the UpgradeServiceInterface
        $upgradeService = $this->createMock(UpgradeServiceInterface::class);

        // Configure the mock to return a failed result
        $upgradeService->expects($this->once())
            ->method('upgrade')
            ->willReturn($this->createFailedExecutionDto());

        // Create a mock for InputInterface and OutputInterface
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // Create an instance of the UpgraderConsole and inject the mock UpgradeServiceInterface
        $upgraderConsole = new UpgraderConsole($upgradeService);

        // Call the execute method
        $result = $upgraderConsole->execute($input, $output);

        // Assert that the result is a failure
        $this->assertSame(UpgraderConsole::FAILURE, $result);
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    private function createSuccessfulExecutionDto(): StepsResponseDto
    {
        $dto = new StepsResponseDto();
        $dto->setIsSuccessful(true);

        return $dto;
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    private function createFailedExecutionDto(): StepsResponseDto
    {
        $dto = new StepsResponseDto();
        $dto->setIsSuccessful(false);

        return $dto;
    }
}
