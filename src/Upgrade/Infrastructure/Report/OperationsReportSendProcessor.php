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
use Upgrade\Infrastructure\Report\ReportSender\ReportSenderInterface;

class OperationsReportSendProcessor implements ReportSendProcessorInterface
{
    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var \Upgrade\Infrastructure\Report\Builder\ReportDtoBuilderInterface
     */
    protected ReportDtoBuilderInterface $reportDtoBuilder;

    /**
     * @var \Upgrade\Infrastructure\Report\ReportSender\ReportSenderInterface
     */
    protected ReportSenderInterface $reportSender;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \Upgrade\Infrastructure\Report\Builder\ReportDtoBuilderInterface $reportDtoBuilder
     * @param \Upgrade\Infrastructure\Report\ReportSender\ReportSenderInterface $reportSender
     * @param \Psr\Log\LoggerInterface $logger
     */
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

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
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
