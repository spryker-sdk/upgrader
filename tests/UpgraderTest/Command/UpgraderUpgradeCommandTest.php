<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgraderTest\Command;

use Codeception\Test\Unit;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Upgrader\Business\CommandExecutor\CommandResultDto;
use Upgrader\Business\Composer\ComposerClient;
use Upgrader\Business\Git\GitClient;
use Upgrader\Business\UpgraderBusinessFactory;
use Upgrader\Business\UpgraderFacade;

/**
 * @group Upgrader
 * @group Command
 * @group UpgraderUpgradeCommandTest
 */
class UpgraderUpgradeCommandTest extends Unit
{
    /**
     * @return void
     */
    public function testUpgradeExistUncommittedChangesReturnNotZeroCode(): void
    {
        $input = $this->makeEmpty(InputInterface::class);
        $output = $this->makeEmpty(OutputInterface::class);
        $io = $this->makeEmpty(SymfonyStyle::class);
        $factory = $this->getMockUpgraderBusinessFactory(true);

        $upgraderFacade = $this->make(UpgraderFacade::class, [
            'createSymfonyStyle' => function () use ($io) {
                return $io;
            },
            'getFactory' => function () use ($factory) {
                return $factory;
            },
        ]);

        $resultCode = $upgraderFacade->upgrade($input, $output);

        $this->assertNotEquals(0, $resultCode);
    }

    /**
     * @return void
     */
    public function testUpgradeReturnZeroCode(): void
    {
        $input = $this->makeEmpty(InputInterface::class);
        $output = $this->makeEmpty(OutputInterface::class);
        $io = $this->makeEmpty(SymfonyStyle::class);
        $factory = $this->getMockUpgraderBusinessFactory(false);

        $upgraderFacade = $this->make(UpgraderFacade::class, [
            'createSymfonyStyle' => function () use ($io) {
                return $io;
            },
            'getFactory' => function () use ($factory) {
                return $factory;
            },
        ]);

        $resultCode = $upgraderFacade->upgrade($input, $output);

        $this->assertEquals(0, $resultCode);
    }


    /**
     * @return UpgraderBusinessFactory
     * @throws Exception
     */
    protected function getMockUpgraderBusinessFactory(bool $hasUncommittedChanges): UpgraderBusinessFactory
    {
        $gitClient = $this->make(GitClient::class, [
            'isUncommittedChangesExist' => function () use ($hasUncommittedChanges){
                return $hasUncommittedChanges;
            },
        ]);

        $composerClient = $this->make(ComposerClient::class, [
            'runComposerUpdate' => function () {
                return new CommandResultDto(0, 'success');
            },
        ]);

        $businessFactory = $this->make(UpgraderBusinessFactory::class, [
            'createGitClient' => function () use ($gitClient) {
                return $gitClient;
            },
            'createComposerClient' => function () use ($composerClient){
                return $composerClient;
            },
        ]);

        return $businessFactory;
    }
}
