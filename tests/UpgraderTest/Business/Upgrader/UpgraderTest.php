<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgraderTest\Business\Upgrader;

use Codeception\Test\Unit;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\PackageManager\Client\Composer\ComposerClient;
use Upgrader\Business\PackageManager\PackageManager;
use Upgrader\Business\Command\Executor\CommandExecutor;
use Upgrader\Business\VersionControlSystem\Client\Git\Command\GitUpdateIndexCommand;
use Upgrader\Business\VersionControlSystem\Client\Git\GitClient;
use Upgrader\Business\VersionControlSystem\VersionControlSystem;

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
        // Arrange
        $packageManager = $this->getMockPackageManager();
        $versionControlSystem = $this->getMockVersionControlSystem(true);
        $upgrader = new \Upgrader\Business\Command\Executor\CommandExecutor($packageManager, $versionControlSystem);

        // Act
        $upgradeResult = $upgrader->upgrade();

        // Assert
        $this->assertFalse($upgradeResult->isSuccess());
        $this->assertStringContainsString('Please commit or revert your changes', $upgradeResult->getMessage());
    }

    /**
     * @return void
     */
    public function testUpgradeReturnZeroCode(): void
    {
        // Arrange
        $packageManager = $this->getMockPackageManager();
        $versionControlSystem = $this->getMockVersionControlSystem(false);
        $upgrader = new \Upgrader\Business\Command\Executor\CommandExecutor($packageManager, $versionControlSystem);

        // Act
        $upgradeResult = $upgrader->upgrade();

        // Assert
        $this->assertTrue($upgradeResult->isSuccess());
    }

    /**
     * @param bool $hasUncommittedChanges
     *
     * @return \Upgrader\Business\VersionControlSystem\VersionControlSystem
     */
    protected function getMockVersionControlSystem(bool $hasUncommittedChanges): VersionControlSystem
    {
        $gitUpdateIndexCommand = $this->make(GitUpdateIndexCommand::class, [
            'run' => function () use ($hasUncommittedChanges) {
                $statusCode = $hasUncommittedChanges ? 1 : 0;

                return new CommandResultOutput($statusCode, '');
            },
        ]);
        $vcsClient = new GitClient($gitUpdateIndexCommand);

        return new VersionControlSystem($vcsClient);
    }

    /**
     * @return \Upgrader\Business\PackageManager\PackageManager
     */
    protected function getMockPackageManager(): PackageManager
    {
        $packageManagerClient = $this->make(ComposerClient::class, [
            'runUpdate' => function () {
                return new CommandResultOutput(0, 'success');
            },
        ]);

        return new PackageManager($packageManagerClient);
    }
}
