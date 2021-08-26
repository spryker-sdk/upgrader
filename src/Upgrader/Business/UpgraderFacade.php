<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpgraderFacade implements UpgraderFacadeInterface
{
    public const SUCCESS_RESULT_CODE = 0;
    public const ERROR_RESULT_CODE = 1;

    /**
     * @var \Upgrader\Business\UpgraderBusinessFactory
     */
    protected $factory;

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function upgrade(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Pre upgrade checking ....');
        $hasUncomitedChanges = $this->getFactory()->createGitClient()->isUncomitedChangesExist();
        if ($hasUncomitedChanges) {
            $io->error('Please commit or revert your changes');

            return static::ERROR_RESULT_CODE;
        }
        $io->writeln('Pre upgrade checking done');

        $io->writeln('Composer update progress ....');
        $this->getFactory()->createComposerClient()->runComposerUpdate();
        $io->success('Composer update done');

        return static::SUCCESS_RESULT_CODE;
    }

    /**
     * @return \Upgrader\Business\UpgraderBusinessFactory
     */
    protected function getFactory(): UpgraderBusinessFactory
    {
        if ($this->factory === null) {
            $this->factory = new UpgraderBusinessFactory();
        }

        return $this->factory;
    }
}
