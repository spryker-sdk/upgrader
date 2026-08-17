<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher;

use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\ChangedXmlFilesFetcher;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;

class ChangedXmlFilesFetcherTest extends TestCase
{
    public function testFetchChangedXmlSchemaFilesShouldReturnEmptyArrayWhenCommandOutputIsEmpty(): void
    {
        // Arrange
        $processRunnerMock = $this->createProcessRunnerServiceMock($this->getExpectedCommandString(), '');

        $changedXmlFilesFetcher = new ChangedXmlFilesFetcher($processRunnerMock);

        // Act
        $result = $changedXmlFilesFetcher->fetchChangedXmlSchemaFiles('/vendor', '/rev-vendor');

        // Assert
        $this->assertEmpty($result);
    }

    public function testFetchChangedXmlSchemaFilesShouldReturnChangesLines(): void
    {
        // Arrange
        $processRunnerMock = $this->createProcessRunnerServiceMock(
            $this->getExpectedCommandString(),
            <<<OUT
            src/Spryker/Zed/ApiKey/Persistence/Propel/Schema
            src/Spryker/Zed/Acl/Persistence/Propel/Schema
            OUT,
        );

        $changedXmlFilesFetcher = new ChangedXmlFilesFetcher($processRunnerMock);

        // Act
        $result = $changedXmlFilesFetcher->fetchChangedXmlSchemaFiles('/vendor', '/rev-vendor');

        // Assert
        $this->assertSame(['src/Spryker/Zed/ApiKey/Persistence/Propel/Schema', 'src/Spryker/Zed/Acl/Persistence/Propel/Schema'], $result);
    }

    protected function createProcessRunnerServiceMock(string $expectedCommand, string $output = ''): ProcessRunnerServiceInterface
    {
        $process = $this->createMock(Process::class);
        $process->method('getOutput')->willReturn($output);

        $processRunnerService = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerService->expects($this->once())
            ->method('mustRunFromCommandLine')
            ->with($expectedCommand)->willReturn($process);

        return $processRunnerService;
    }

    protected function getExpectedCommandString(): string
    {
        return <<<'CMD'
        diff -qNr '/rev-vendor' '/vendor' | \
        grep 'schema.xml' | grep 'vendor/spryker' | \
        (grep 'Only in /rev-vendor:\|Files /rev-vendor' || true) | \
        sed -E 's/^Only in \/rev-vendor: //' | \
        sed -E 's/Files \/rev-vendor(\S+)(.*)/\1/'
        CMD;
    }
}
