<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Adapter;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Upgrade\Application\Adapter\IntegratorAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;

class IntegratorAdapter implements IntegratorAdapterInterface
{
    /**
     * @var string
     */
    protected const RUNNER = '/vendor/bin/integrator';

    /**
     * @var string
     */
    protected const NO_INTERACTION_COMPOSER_FLAG = '--no-interaction';

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     */
    public function __construct(ProcessRunnerServiceInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function runIntegrator(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = sprintf('%s %s', APPLICATION_ROOT_DIR . static::RUNNER, static::NO_INTERACTION_COMPOSER_FLAG);
        $process = $this->processRunner->run(explode(' ', $command));

        $stepsExecutionDto->setIsSuccessful($process->isSuccessful());
        if (!$stepsExecutionDto->getIsSuccessful()) {
            $output = $process->getErrorOutput() ?: $process->getOutput();
            $stepsExecutionDto->addOutputMessage(
                $command . PHP_EOL . $output. PHP_EOL . 'Error code:' . $process->getExitCode(),
            );
        }

        return $stepsExecutionDto;
    }
}
