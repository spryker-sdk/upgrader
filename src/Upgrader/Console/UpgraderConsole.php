<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

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
    protected const NAME = 'upgradability:php:upgrade';

    /**
     * @var string
     */
    protected const DESCRIPTION = 'Automatic project updates.';

    protected UpgradeServiceInterface $upgradeService;

    public function __construct(UpgradeServiceInterface $upgradeService)
    {
        parent::__construct();
        $this->upgradeService = $upgradeService;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $stepsExecutionDto = $this->upgradeService->upgrade();

        $output->writeln((string)$stepsExecutionDto->getOutputMessage());

        if (!$stepsExecutionDto->getIsSuccessful() || $stepsExecutionDto->hasBlockers()) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
