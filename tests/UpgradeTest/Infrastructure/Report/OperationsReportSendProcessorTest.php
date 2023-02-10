<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\Report;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Infrastructure\Report\Builder\ReportDtoBuilderInterface;
use Upgrade\Infrastructure\Report\Dto\ReportDto;
use Upgrade\Infrastructure\Report\OperationsReportSendProcessor;
use Upgrade\Infrastructure\Report\Sender\ReportSenderInterface;

/**
 * @group UpgradeTest
 * @group Infrastructure
 * @group Report
 * @group OperationsReportSendProcessorTest
 */
class OperationsReportSendProcessorTest extends TestCase
{
    /**
     * @return void
     */
    public function testProcessShouldSkipWhenReportingDisabled(): void
    {
        // Arrange
        $operationsReportSendProcessor = new OperationsReportSendProcessor(
            $this->createConfigurationProviderMock(false),
            $this->createReportDtoBuilderMock(),
            $this->createReportSenderMock(false),
            $this->createLoggerMock(false),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $operationsReportSendProcessor->process($stepsResponseDto);
    }

    /**
     * @return void
     */
    public function testProcessShouldSendReport(): void
    {
        // Arrange
        $operationsReportSendProcessor = new OperationsReportSendProcessor(
            $this->createConfigurationProviderMock(),
            $this->createReportDtoBuilderMock(),
            $this->createReportSenderMock(true),
            $this->createLoggerMock(false),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $operationsReportSendProcessor->process($stepsResponseDto);
    }

    /**
     * @return void
     */
    public function testProcessShouldLogErrorWhenExceptionThrown(): void
    {
        // Arrange
        $operationsReportSendProcessor = new OperationsReportSendProcessor(
            $this->createConfigurationProviderMock(),
            $this->createReportDtoBuilderMock(),
            $this->createReportSenderMock(true, true),
            $this->createLoggerMock(true),
        );

        $stepsResponseDto = new StepsResponseDto();

        // Act
        $operationsReportSendProcessor->process($stepsResponseDto);
    }

    /**
     * @param bool $isReportingEnabled
     *
     * @return \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected function createConfigurationProviderMock(bool $isReportingEnabled = true): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProviderInterface::class);
        $configurationProvider->method('isReportingEnabled')->willReturn($isReportingEnabled);

        return $configurationProvider;
    }

    /**
     * @return \Upgrade\Infrastructure\Report\Builder\ReportDtoBuilderInterface
     */
    protected function createReportDtoBuilderMock(): ReportDtoBuilderInterface
    {
        $reportDtoBuilder = $this->createMock(ReportDtoBuilderInterface::class);

        $reportDtoBuilder->method('buildFromStepResponseDto')->willReturn($this->createMock(ReportDto::class));

        return $reportDtoBuilder;
    }

    /**
     * @param bool $shouldBeSent
     * @param bool $throwException
     *
     * @return \Upgrade\Infrastructure\Report\Sender\ReportSenderInterface
     */
    protected function createReportSenderMock(bool $shouldBeSent, bool $throwException = false): ReportSenderInterface
    {
        $reportSender = $this->createMock(ReportSenderInterface::class);

        $method = $reportSender->expects($shouldBeSent ? $this->once() : $this->never())->method('send');

        if ($throwException) {
            $method->willThrowException(new RuntimeException(''));
        }

        return $reportSender;
    }

    /**
     * @param bool $shouldTriggerLogging
     *
     * @return \Psr\Log\LoggerInterface
     */
    protected function createLoggerMock(bool $shouldTriggerLogging): LoggerInterface
    {
        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects($shouldTriggerLogging ? $this->once() : $this->never())->method('error');

        return $logger;
    }
}
