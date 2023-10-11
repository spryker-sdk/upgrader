<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Step;

use Core\Infrastructure\Service\Filesystem;
use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\Step\ComposerJsonConstraintFixStep;
use Upgrader\Configuration\ConfigurationProvider;

class ComposerJsonConstraintFixStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testRunShouldSkipStepWhenGrepNothingFound(): void
    {
        // Arrange
        $process = $this->createProcessMock('', 1);

        $composerJsonConstraintFixStep = new ComposerJsonConstraintFixStep(
            $this->createMock(VersionControlSystemAdapterInterface::class),
            $this->createProcessRunnerServiceMock($process),
            $this->createFilesystemMock(false),
            new ConfigurationProvider(),
        );

        $stepResponseDto = new StepsResponseDto();

        // Act
        $returnStepsResponse = $composerJsonConstraintFixStep->run($stepResponseDto);

        // Assert
        $this->assertSame($stepResponseDto, $returnStepsResponse);
    }

    /**
     * @return void
     */
    public function testRunShouldSkipStepWhenNoPackagesFound(): void
    {
        // Arrange
        $process = $this->createProcessMock();

        $composerJsonConstraintFixStep = new ComposerJsonConstraintFixStep(
            $this->createMock(VersionControlSystemAdapterInterface::class),
            $this->createProcessRunnerServiceMock($process),
            $this->createFilesystemMock(false),
            new ConfigurationProvider(),
        );

        $stepResponseDto = new StepsResponseDto();

        // Act
        $returnStepsResponse = $composerJsonConstraintFixStep->run($stepResponseDto);

        // Assert
        $this->assertSame($stepResponseDto, $returnStepsResponse);
    }

    /**
     * @return void
     */
    public function testRunShouldWriteUpdatedJson(): void
    {
        // Arrange
        $process = $this->createProcessMock(
            <<<OUT
            +        "spryker-feature/invoice": "202212.0",
            +        "spryker-shop/test-invalid": "1.2.3-beta",
            +        "spryker-shop/test-invalid-one": "dev-branch",
            +        "spryker-shop/test-valid": "2",
            +        "spryker-shop/test-valid-one": "2.1",
            +        "spryker-shop/test-valid-two": "2.1.1",
            +        "spryker/company-users-rest-api": "2.6.0",
            +        "spryker/oauth-api": "1.0.0",
            +        "spryker/uuid": "1.1.0",
            +        "spryker/acl": "1.1.*",
            +        "symfony/kernel": "6.1.*",
            +        "symfony/uuid": "5.1.0",
            OUT,
        );

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('readFile')->willReturn(
            <<<FILE
            {
                "name": "spryker-sdk/upgrader",
                "type": "library",
                "description": "Code evaluator and upgrader tool",
                "license": "proprietary",
                "require": {
                    "spryker-feature/invoice": "202212.0",
                    "spryker-shop/test-invalid": "1.2.3-beta",
                    "spryker-shop/test-invalid-one": "dev-branch",
                    "spryker-shop/test-valid": "2",
                    "spryker-shop/test-valid-one": "2.1",
                    "spryker-shop/test-valid-two": "2.1.1",
                    "spryker/company-users-rest-api": "2.6.0",
                    "spryker/oauth-api": "1.0.0",
                    "symfony/finder": "~5.0.0",
                    "spryker/nopayment": "dev-feature/ticket",
                    "spryker/uuid": "1.1.0",
                    "spryker/acl": "1.1.*",
                    "symfony/kernel": "6.1.*",
                    "symfony/uuid": "5.1.0"
                }
            FILE,
        );
        $filesystem->expects($this->once())->method('dumpFile')->with($this->stringEndsWith('composer.json'), $this->equalTo(
            <<<FILE
            {
                "name": "spryker-sdk/upgrader",
                "type": "library",
                "description": "Code evaluator and upgrader tool",
                "license": "proprietary",
                "require": {
                    "spryker-feature/invoice": "^202212.0",
                    "spryker-shop/test-invalid": "1.2.3-beta",
                    "spryker-shop/test-invalid-one": "dev-branch",
                    "spryker-shop/test-valid": "^2",
                    "spryker-shop/test-valid-one": "^2.1",
                    "spryker-shop/test-valid-two": "^2.1.1",
                    "spryker/company-users-rest-api": "^2.6.0",
                    "spryker/oauth-api": "^1.0.0",
                    "symfony/finder": "~5.0.0",
                    "spryker/nopayment": "dev-feature/ticket",
                    "spryker/uuid": "^1.1.0",
                    "spryker/acl": "1.1.*",
                    "symfony/kernel": "6.1.*",
                    "symfony/uuid": "5.1.0"
                }
            FILE,
        ));

        $composerJsonConstraintFixStep = new ComposerJsonConstraintFixStep(
            $this->createMock(VersionControlSystemAdapterInterface::class),
            $this->createProcessRunnerServiceMock($process),
            $filesystem,
            new ConfigurationProvider(),
        );

        $stepResponseDto = new StepsResponseDto();

        // Act
        $returnStepsResponse = $composerJsonConstraintFixStep->run($stepResponseDto);

        // Assert
        $this->assertSame($stepResponseDto, $returnStepsResponse);
    }

    /**
     * @param bool $expectWriting
     *
     * @return \Core\Infrastructure\Service\Filesystem
     */
    protected function createFilesystemMock(bool $expectWriting = true): Filesystem
    {
        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->expects($expectWriting ? $this->once() : $this->never())->method('dumpFile');

        return $filesystem;
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected function createProcessRunnerServiceMock(Process $process): ProcessRunnerServiceInterface
    {
        $processRunnerService = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerService->method('runFromCommandLine')->willReturn($process);

        return $processRunnerService;
    }

    /**
     * @param string $output
     * @param int $exitCode
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function createProcessMock(string $output = '', int $exitCode = 0): Process
    {
        $process = $this->createMock(Process::class);
        $process->method('getExitCode')->willReturn($exitCode);
        $process->method('getOutput')->willReturn($output);

        return $process;
    }
}
