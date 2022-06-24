<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Upgrade\Application\Service\UpgradeServiceInterface;

class UpgraderConsole extends Command
{
    /**
     * @var string
     */
    protected const NAME = 'upgrader:codebase';

    /**
     * @var string
     */
    protected const DESCRIPTION = 'Automatic project updates.';

    /**
     * @var \Upgrade\Application\Service\UpgradeServiceInterface
     */
    protected UpgradeServiceInterface $upgradeService;

    /**
     * @param \Upgrade\Application\Service\UpgradeServiceInterface $upgradeService
     */
    public function __construct(UpgradeServiceInterface $upgradeService)
    {
        parent::__construct();
        $this->upgradeService = $upgradeService;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $stepsExecutionDto = $this->upgradeService->upgrade();

        $output->writeln((string)$stepsExecutionDto->getOutputMessage());

        return Command::SUCCESS;
    }
}
