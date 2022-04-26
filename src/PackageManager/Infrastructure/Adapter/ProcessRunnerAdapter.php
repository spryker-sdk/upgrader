<?php

namespace PackageManager\Infrastructure\Adapter;

use PackageManager\Domain\ProcessRunner\ProcessRunnerInterface;
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
    public function runCommand(array $command): Process
    {
        return $this->processRunnerService->runCommand($command);
    }
}
