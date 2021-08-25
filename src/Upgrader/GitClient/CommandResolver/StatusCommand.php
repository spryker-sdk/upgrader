<?php

namespace Upgrader\GitClient\CommandResolver;

use Symfony\Component\Process\Process;

class StatusCommand
{
    /**
     *
     * @codeCoverageIgnore
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param string $resolvedCommitMessage
     *
     * @return void
     */
    public function exec(): void
    {
        $command = ['git', 'update-index', '--refresh'];
        $process = new Process($command, (string)getcwd());
        $process->run();
        var_dump($process->getExitCode());
        var_dump($process->getExitCodeText());
        var_dump($process->getErrorOutput());
    }
}
