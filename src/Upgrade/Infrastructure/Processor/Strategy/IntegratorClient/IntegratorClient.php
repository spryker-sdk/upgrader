<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\IntegratorClient;

use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Process\ProcessRunner;

class IntegratorClient implements IntegratorClientInterface
{
    /**
     * @var string
     */
    protected const RUNNER = '/home/spryker/.composer/vendor/bin/integrator';

    /**
     * @var string
     */
    protected const FLAG = '--no-interaction';

    /**
     * @var \Upgrade\Infrastructure\Process\ProcessRunner
     */
    protected ProcessRunner $processRunner;

    /**
     * @param \Upgrade\Infrastructure\Process\ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function runIntegrator(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = sprintf('%s %s', static::RUNNER, static::FLAG);
        $process = $this->processRunner->run(explode(' ', $command));

        $stepsExecutionDto->setIsSuccessful(!$process->getExitCode());
        if (!$stepsExecutionDto->getIsSuccessful()) {
            $stepsExecutionDto->setOutputMessage(
                $command . PHP_EOL . $process->getErrorOutput() . PHP_EOL . 'Error code:' . $process->getExitCode(),
            );
        }

        return $stepsExecutionDto;
    }
}
