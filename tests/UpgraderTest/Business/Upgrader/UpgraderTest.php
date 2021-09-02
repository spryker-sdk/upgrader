<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgraderTest\Business\Upgrader;

use Codeception\Test\Unit;
use Upgrader\Business\Command\CommandResult;
use Upgrader\Business\ComposerClient\ComposerClient;
use Upgrader\Business\GitClient\GitClient;
use Upgrader\Business\Upgrader\Upgrader;
use Upgrader\Business\UpgraderBusinessFactory;

/**
 * @group Upgrader
 * @group Business
 * @group UpgraderTest
 */
class UpgraderTest extends Unit
{
    /**
     * @return void
     */
    public function testIsUpgradeAvailableExistUncommittedChanges(): void
    {
        $factory = $this->getMockUpgraderBusinessFactory(true);

        $upgrader = $this->make(Upgrader::class, [
            'getFactory' => function () use ($factory) {
                return $factory;
            },
        ]);

        $upgraderResult = $upgrader->isUpgradeAvailable();

        $this->assertFalse($upgraderResult->isSuccess());
        $this->assertStringContainsString('Please commit or revert your changes', $upgraderResult->getMessage());
    }

    /**
     * @return void
     */
    public function testUpgradeReturnZeroCode(): void
    {
        $factory = $this->getMockUpgraderBusinessFactory(false);

        $upgraderFacade = $this->make(Upgrader::class, [
            'getFactory' => function () use ($factory) {
                return $factory;
            },
        ]);

        $upgradeResult = $upgraderFacade->upgrade();

        $this->assertTrue($upgradeResult->isSuccess());
    }

    /**
     * @param bool $hasUncommittedChanges
     *
     * @return \Upgrader\Business\UpgraderBusinessFactory
     */
    protected function getMockUpgraderBusinessFactory(bool $hasUncommittedChanges): UpgraderBusinessFactory
    {
        $gitClient = $this->make(GitClient::class, [
            'isUncommittedChangesExist' => function () use ($hasUncommittedChanges) {
                return $hasUncommittedChanges;
            },
        ]);

        $composerClient = $this->make(ComposerClient::class, [
            'runComposerUpdate' => function () {
                return new CommandResult(0, 'success');
            },
        ]);

        $businessFactory = $this->make(UpgraderBusinessFactory::class, [
            'createGitClient' => function () use ($gitClient) {
                return $gitClient;
            },
            'createComposerClient' => function () use ($composerClient) {
                return $composerClient;
            },
        ]);

        return $businessFactory;
    }
}
