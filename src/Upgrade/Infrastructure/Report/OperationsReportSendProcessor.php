<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report;

use Psr\Log\LoggerInterface;
use Throwable;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Report\ReportSendProcessorInterface;
use Upgrade\Infrastructure\Report\Builder\ReportDtoBuilderInterface;
use Upgrade\Infrastructure\Report\Sender\ReportSenderInterface;

class OperationsReportSendProcessor implements ReportSendProcessorInterface
{
    protected ConfigurationProviderInterface $configurationProvider;

    protected ReportDtoBuilderInterface $reportDtoBuilder;

    protected ReportSenderInterface $reportSender;

    protected LoggerInterface $logger;

    public function __construct(
        ConfigurationProviderInterface $configurationProvider,
        ReportDtoBuilderInterface $reportDtoBuilder,
        ReportSenderInterface $reportSender,
        LoggerInterface $logger
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->reportDtoBuilder = $reportDtoBuilder;
        $this->reportSender = $reportSender;
        $this->logger = $logger;
    }

    public function process(StepsResponseDto $stepsResponseDto): StepsResponseDto
    {
        if (!$this->configurationProvider->isReportingEnabled()) {
            return $stepsResponseDto;
        }

        try {
            $reportDto = $this->reportDtoBuilder->buildFromStepResponseDto($stepsResponseDto);

            $this->reportSender->send($reportDto);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $stepsResponseDto;
    }
}
