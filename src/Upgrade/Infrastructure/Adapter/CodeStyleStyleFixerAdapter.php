<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Adapter;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Upgrade\Application\Adapter\CodeStyleFixerAdapterInterface;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;

class CodeStyleStyleFixerAdapter implements CodeStyleFixerAdapterInterface
{
    /**
     * @var string
     */
    protected const RUNNER = '/vendor/bin/phpcbf';

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @var \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface
     */
    protected VersionControlSystemAdapterInterface $gitAdapter;

    /**
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $gitAdapter
     */
    public function __construct(ProcessRunnerServiceInterface $processRunner, VersionControlSystemAdapterInterface $gitAdapter)
    {
        $this->processRunner = $processRunner;
        $this->gitAdapter = $gitAdapter;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function runCodeFixer(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if (!$stepsExecutionDto->getChangedFiles()) {
            return $stepsExecutionDto;
        }

        $command = sprintf('%s %s', APPLICATION_ROOT_DIR . static::RUNNER, implode(' ', $stepsExecutionDto->getChangedFiles()));
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
