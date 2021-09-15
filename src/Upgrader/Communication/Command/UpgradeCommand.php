<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Communication\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\CommandResponse;

class UpgradeCommand extends AbstractCommand
{
    protected const NAME = 'upgrade';
    protected const DESCRIPTION = 'Upgrade Spryker packages.';
    protected const OPTION_FILTER = 'filter';
    protected const OPTION_FILTER_SHORT = 'f';
    protected const OPTION_FILTER_DESCRIPTION = 'List commands, that will be filtered (for multiple commands use comma)';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION)
            ->setHelp($this->getHelpInfo())
            ->addOption(
                static::OPTION_FILTER,
                static::OPTION_FILTER_SHORT,
                InputOption::VALUE_OPTIONAL,
                static::OPTION_FILTER_DESCRIPTION
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $filterList = $input->getOption(static::OPTION_FILTER);
        $commandRequest = new CommandRequest($filterList);

        $commandResponseList = $this->getFacade()->upgrade($commandRequest);

        foreach ($commandResponseList->getCommandResponses() as $commandResponse) {
            $this->processOutput($commandResponse, $output);
        }

        if ($commandResponseList->getExitCode()) {
            $output->writeln('<error>Upgrade command has been finished with errors</error>');

            return $commandResponseList->getExitCode();
        }

        $output->writeln('<info>Upgrade command has been finished successfully</info>');

        return $commandResponseList->getExitCode();
    }

    /**
     * @codeCoverageIgnore
     *
     * @param \Upgrader\Business\Command\CommandResponse $response
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function processOutput(CommandResponse $response, OutputInterface $output): int
    {
        $output->writeln('<comment>' . str_repeat('=', 100) . '</comment>');
        $output->writeln('<comment>' . $response->getUpgraderCommand() . '</comment>');
        $output->writeln('<comment>' . str_repeat('-', 100) . '</comment>');
        $output->writeln(
            '<comment>' . str_replace('\'', '', $response->getProcessCommand()) . ' </comment>',
            OutputInterface::VERBOSITY_VERBOSE
        );

        if ($response->getExitCode()) {
            $output->writeln('<fg=red>' . $response->getOutput() . '</>');
            $output->writeln('<fg=red>' . $response->getErrorOutput() . '</>');
            $output->writeln('<error>Finished with errors</error>');
            $output->writeln('<comment>' . str_repeat('=', 100) . '</comment>');

            return $response->getExitCode();
        }

        $output->writeln('<fg=green>' . $response->getOutput() . '</>', OutputInterface::VERBOSITY_VERBOSE);
        $output->writeln('<info>Finished successfully</info>');
        $output->writeln('<comment>' . str_repeat('=', 100) . '</comment>');

        return $response->getExitCode();
    }

    /**
     * @return string
     */
    protected function getHelpInfo(): string
    {
        $commands = $this->getFacade()->getUpgraderCommands();

        $help = '';
        foreach ($commands as $command) {
            $help .= sprintf(
                "<info>%s</info> %s %s \n",
                $command->getName(),
                str_repeat(' ', 21 - strlen($command->getName())),
                $command->getDescription()
            );
        }

        return $help;
    }
}

///**
// * @param \Symfony\Component\Console\Input\InputInterface $input
// * @param \Symfony\Component\Console\Output\OutputInterface $output
// *
// * @return int
// */
//public function execute(InputInterface $input, OutputInterface $output): int
//{
//    $resultCollection = $this->getFacade()->upgrade();
//
//    /** @var \Upgrader\Business\Command\ResultOutput\CommandResultOutput $result */
//    foreach ($resultCollection->toArray() as $result) {
//        $this->printResult($output, $result);
//    }
//
//    if (!$resultCollection->isSuccess()) {
//        $output->writeln('<fg=red;options=bold>Upgrade command finished with error.</>.');
//
//        return static::CODE_ERROR;
//    }
//
//    $output->writeln('<fg=green;options=bold>Upgrade command has been finished successfully.</>.');
//
//    return static::CODE_SUCCESS;
//}
