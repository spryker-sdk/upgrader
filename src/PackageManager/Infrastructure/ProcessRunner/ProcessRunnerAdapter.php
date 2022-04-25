<?php

namespace PackageManager\Infrastructure\ProcessRunner;

use PackageManager\Domain\Client\ProcessRunnerInterface;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ProcessRunner\Application\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;

class ProcessRunnerAdapter implements ProcessRunnerInterface
{
    /**
     * @var \ProcessRunner\Application\Service\ProcessRunnerServiceInterface
     */
    protected $processRunnerService;

    /**
     * @param \ProcessRunner\Application\Service\ProcessRunnerServiceInterface $processRunnerService
     */
    public function __construct(ProcessRunnerServiceInterface $processRunnerService)
    {
        $this->processRunnerService = $processRunnerService;
    }

    /**
     * @param array $command
     * @return PackageManagerResponseDto
     */
    public function runProcess(array $command): PackageManagerResponseDto
    {
        $process = $this->processRunnerService->runProcess($command);

        return $this->createResponse($process);
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function createResponse(Process $process): PackageManagerResponseDto
    {
        $command = str_replace('\'', '', $process->getCommandLine());
        $output = $process->getExitCode() ? $process->getErrorOutput() : '';
        $outputs = array_filter([$command, $output]);

        return new PackageManagerResponseDto($process->isSuccessful(), implode(PHP_EOL, $outputs));
    }

}
