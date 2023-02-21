<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Executor;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Upgrade\Application\Adapter\IntegratorExecutorInterface;
use Upgrade\Application\Dto\IntegratorResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;

class IntegratorExecutor implements IntegratorExecutorInterface
{
    /**
     * @var string
     */
    protected const RUNNER = '/vendor/bin/integrator';

    /**
     * @var string
     */
    protected const MANIFEST_LOCK_RUNNER = 'manifest:lock:run';

    /**
     * @var string
     */
    protected const NO_INTERACTION_COMPOSER_FLAG = '--no-interaction';

    /**
     * @var string
     */
    protected const FROMAT_JSON_OPTION = '--format=json';

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
        $command = implode(' ', [
            APPLICATION_ROOT_DIR . static::RUNNER,
            static::NO_INTERACTION_COMPOSER_FLAG,
            static::FROMAT_JSON_OPTION,
            ]);

        return $this->runIntegratorProcess($command, $stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function runIntegratorLockUpdater(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $command = implode(' ', [
            APPLICATION_ROOT_DIR . static::RUNNER,
            static::MANIFEST_LOCK_RUNNER,
            static::NO_INTERACTION_COMPOSER_FLAG,
            static::FROMAT_JSON_OPTION,
        ]);

        $this->runIntegratorProcess($command, $stepsExecutionDto);
    }

    /**
     * @param string $command
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    protected function runIntegratorProcess(string $command, StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $process = $this->processRunner->run(explode(' ', $command));

        $stepsExecutionDto->setIsSuccessful($process->isSuccessful());
        if (!$stepsExecutionDto->getIsSuccessful()) {
            $output = $process->getErrorOutput() ?: $process->getOutput();
            $stepsExecutionDto->addOutputMessage(
                $command . PHP_EOL . $output . PHP_EOL . 'Error code:' . $process->getExitCode(),
            );
        }

        $integratorOutput = json_decode($process->getOutput(), true);
        if (is_array($integratorOutput)) {
            $stepsExecutionDto->setIntegratorResponseDto(new IntegratorResponseDto($integratorOutput));
        }

        return $stepsExecutionDto;
    }
}
