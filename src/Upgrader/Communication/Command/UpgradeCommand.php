<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Communication\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Upgrader\Business\Upgrader\Enum\UpgradeStrategyEnum;
use Upgrader\Business\Upgrader\Request\UpgraderRequest;
use Upgrader\Business\Upgrader\Response\UpgraderResponseInterface;

class UpgradeCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected const NAME = 'upgrade';

    /**
     * @var string
     */
    protected const DESCRIPTION = 'Upgrade Spryker packages.';

    /**
     * @var string
     */
    protected const OPTION_STRATEGY = 'strategy';

    /**
     * @var string
     */
    protected const OPTION_STRATEGY_SHORT = 's';

    /**
     * @var string
     */
    protected const OPTION_STRATEGY_DESCRIPTION = 'Chose update strategy (composer-update or release-group-approach)';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION)
            ->addOption(
                static::OPTION_STRATEGY,
                static::OPTION_STRATEGY_SHORT,
                InputOption::VALUE_OPTIONAL,
                static::OPTION_STRATEGY_DESCRIPTION,
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
        $request = $this->createRequest($input);
        $upgraderResponseCollection = $this->getFacade()->upgrade($request);

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
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \Upgrader\Business\Upgrader\Request\UpgraderRequest
     */
    protected function createRequest(InputInterface $input): UpgraderRequest
    {
        $strategyOption = $input->getOption(static::OPTION_STRATEGY) ?? UpgradeStrategyEnum::COMPOSER_UPDATE;
        $strategyEnum = new UpgradeStrategyEnum($strategyOption);

        return new UpgraderRequest($strategyEnum);
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
