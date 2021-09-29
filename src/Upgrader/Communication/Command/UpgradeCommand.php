<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Communication\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Upgrader\Business\Upgrader\Response\UpgraderResponseInterface;

class UpgradeCommand extends AbstractCommand
{
    protected const NAME = 'upgrade';
    protected const DESCRIPTION = 'Upgrade Spryker packages.';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $upgraderResponseCollection = $this->getFacade()->upgrade();

        foreach ($upgraderResponseCollection->toArray() as $response) {
            $this->processOutput($response, $output);
        }

        if ($upgraderResponseCollection->getExitCode()) {
            $output->writeln('<error>Upgrade command has been finished with errors</error>');

            return $upgraderResponseCollection->getExitCode();
        }

        $output->writeln('<info>Upgrade command has been finished successfully</info>');

        return $upgraderResponseCollection->getExitCode();
    }

    /**
     * @codeCoverageIgnore
     *
     * @param \Upgrader\Business\Upgrader\Response\UpgraderResponseInterface $response
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function processOutput(UpgraderResponseInterface $response, OutputInterface $output): int
    {
        if ($response->getExitCode()) {
            $output->writeln('<fg=red>' . $response->getOutput() . '</>');

            return $response->getExitCode();
        }
        $output->writeln('<fg=green>' . $response->getOutput() . '</>');

        return $response->getExitCode();
    }
}
