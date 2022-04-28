<?php

namespace Upgrade\Infrastructure\Processor\Strategy\Composer\Steps;

use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Process\ProcessRunner;
use Upgrade\Infrastructure\Processor\Strategy\RollbackStepInterface;
use Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver;

class IntegratorStep  extends AbstractStep implements RollbackStepInterface
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
    public function __construct(VersionControlSystemAdapterResolver $vscAdapterResolver, ProcessRunner $processRunner)
    {
        parent::__construct($vscAdapterResolver);

        $this->processRunner = $processRunner;
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $command = sprintf('%s %s', static::RUNNER, static::FLAG);
        $process = $this->processRunner->run(explode(' ', $command));

        $stepsExecutionDto->setIsSuccessful(!$process->getExitCode());
        if(!$stepsExecutionDto->getIsSuccessful()){
            $stepsExecutionDto->setOutputMessage(
                $command . "\n" . $process->getErrorOutput() . "\n Error code:" . $process->getExitCode()
            );
        }
        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function rollBack(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->vsc->restore($stepsExecutionDto);
    }
}
