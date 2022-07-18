<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Console;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Infrastructure\Service\CodebaseService;
use Resolve\Domain\Entity\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Resolve\Application\Service\ResolveServiceInterface;
use Upgrader\Configuration\ConfigurationProvider;

class ResolveConsole extends Command
{
    /**
     * @var string
     */
    protected const NAME = 'resolve:php:code';

    /**
     * @var string
     */
    protected const DESCRIPTION = 'Automatic resolve project updates.';

    /**
     * @var ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @var ResolveServiceInterface
     */
    protected ResolveServiceInterface $resolveService;

    /**
     * @param ResolveServiceInterface $resolveService
     * @param ConfigurationProvider $configurationProvider
     * @param CodebaseService $codebaseService
     */
    public function __construct(
        ResolveServiceInterface $resolveService,
        ConfigurationProvider $configurationProvider,
        CodebaseService $codebaseService
    ) {
        parent::__construct();
        $this->resolveService = $resolveService;
        $this->configurationProvider = $configurationProvider;
        $this->codebaseService = $codebaseService;
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
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Continue to Resolve? [y/N] ');
        if (!in_array(trim(fgets(STDIN)), array('y', 'Y'))) {
            $output->writeln('Stopped');
            return Command::FAILURE;
        }
        $output->writeln('Resolving..');

        $codebaseRequestDto = new CodeBaseRequestDto(
            $this->configurationProvider->getToolingConfigurationFilePath(),
            $this->configurationProvider->getSrcPath(),
            $this->configurationProvider->getCorePaths(),
            $this->configurationProvider->getCoreNamespaces(),
            $this->configurationProvider->getIgnoreSources(),
        );

        $codebaseSourceDto = $this->codebaseService->readCodeBase($codebaseRequestDto);

        $response = $this->resolveService->resolve($codebaseSourceDto);

        $output->writeln($response->getMessage());

        return Command::SUCCESS;
    }
}
