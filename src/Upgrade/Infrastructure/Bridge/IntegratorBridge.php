<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Bridge;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Upgrade\Application\Bridge\IntegratorBridgeInterface;
use Upgrade\Application\Dto\StepsExecutionDto;

class IntegratorBridge implements IntegratorBridgeInterface
{
    /**
     * @var string
     */
    protected const BINARY_INTEGRATOR_PATH = 'vendor/bin/integrator';

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
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function runIntegrator(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = sprintf('%s %s', static::BINARY_INTEGRATOR_PATH, static::NO_INTERACTION_COMPOSER_FLAG);
        $dirname = dirname(__DIR__);
        $position = strpos($dirname, '.composer');
        $isGlobalExecution = $position !== false;
        if ($isGlobalExecution) {
            $command = sprintf(
                '%s %s',
                substr(
                    $dirname,
                    0,
                    $position,
                ) . '.composer' . DIRECTORY_SEPARATOR . static::BINARY_INTEGRATOR_PATH,
                static::NO_INTERACTION_COMPOSER_FLAG,
            );
        }
        $process = $this->processRunner->run(explode(' ', $command));

        $stepsExecutionDto->setIsSuccessful(!$process->getExitCode());
        if (!$stepsExecutionDto->getIsSuccessful()) {
            $stepsExecutionDto->addOutputMessage(
                $command . PHP_EOL . $process->getErrorOutput() . PHP_EOL . 'Error code:' . $process->getExitCode(),
            );
        }

        return $stepsExecutionDto;
    }
}