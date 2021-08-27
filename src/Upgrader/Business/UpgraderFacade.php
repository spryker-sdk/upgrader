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
    public const CODE_SUCCESS = 0;
    public const CODE_ERROR = 1;

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
        $io = $this->createSymfonyStyle($input, $output);

        $output->writeln('Pre upgrade checking ....', OutputInterface::VERBOSITY_VERY_VERBOSE);
        $hasUncommittedChanges = $this->getFactory()->createGitClient()->isUncommittedChangesExist();
        if ($hasUncommittedChanges) {
            $io->error('Please commit or revert your changes');

            return static::CODE_ERROR;
        }
        $io->writeln('Pre upgrade checking done', OutputInterface::VERBOSITY_VERY_VERBOSE);

        $io->writeln('Composer update progress ....', OutputInterface::VERBOSITY_VERY_VERBOSE);
        $this->getFactory()->createComposerClient()->runComposerUpdate();
        $io->success('Composer update done');

        return static::CODE_SUCCESS;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected function createSymfonyStyle(InputInterface $input, OutputInterface $output): SymfonyStyle
    {
        return new SymfonyStyle($input, $output);
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
