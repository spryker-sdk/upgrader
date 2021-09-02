<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Communication\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpgradeCommand extends AbstractCommand
{
    public const COMMAND_NAME = 'upgrade';
    public const COMMAND_DESCRIPTION = 'Upgrade Spryker packages';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->createSymfonyStyle($input, $output);

        $checkResult = $this->getFacade()->isUpgradeAvailable();
        if (!$checkResult->isSuccess()) {
            $io->error((string)$checkResult->getMessage());

            return static::CODE_ERROR;
        }

        $upgradeResult = $this->getFacade()->upgrade();
        if (!$upgradeResult->isSuccess()) {
            $io->error((string)$upgradeResult->getMessage());

            return static::CODE_ERROR;
        }
        $io->success('Composer update done');

        return static::CODE_SUCCESS;
    }
}
