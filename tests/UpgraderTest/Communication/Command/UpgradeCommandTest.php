<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgraderTest\Communication\Command;

use Codeception\Test\Unit;
use Upgrader\Communication\Command\UpgradeCommand;

class UpgradeCommandTest extends Unit
{
    /**
     * @var \UpgraderTest\UpgraderTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testExecuteHasFinished(): void
    {
        // Arrange
        $command = new UpgradeCommand();
        $tester = $this->tester->getConsoleTester($command);

        // Act
        $tester->execute([]);
        $output = $tester->getDisplay();

        // Assert
        $this->assertRegExp('#Upgrade command has been finished successfully#', $output);
    }

    /**
     * @return void
     */
    public function testExecuteGitBranchHasFinished(): void
    {
        // Arrange
        $upgradeCommand = new UpgradeCommand();
        $tester = $this->tester->getConsoleTester($upgradeCommand);
        $command = $this->getGitBranchCommand();

        // Act
        $tester->execute(
            [
                '--filter' => $command->getName(),
            ],
        );
        $output = $tester->getDisplay();

        // Assert
        $this->assertRegExp('#' . $command->getName() . '#', $output);
    }

    /**
     * @return void
     */
    public function testExecuteGitCommitHasFinished(): void
    {
        // Arrange
        $upgradeCommand = new UpgradeCommand();
        $tester = $this->tester->getConsoleTester($upgradeCommand);
        $command = $this->getGitCommitCommand();

        // Act
        $tester->execute(
            [
                '--filter' => $command->getName(),
            ],
        );
        $output = $tester->getDisplay();

        // Assert
        $this->assertRegExp('#' . $command->getName() . '#', $output);
    }

    /**
     * @return void
     */
    public function testExecuteGitPushHasFinished(): void
    {
        // Arrange
        $upgradeCommand = new UpgradeCommand();
        $tester = $this->tester->getConsoleTester($upgradeCommand);
        $command = $this->getGitPushCommand();

        // Act
        $tester->execute(
            [
                '--filter' => $command->getName(),
            ],
        );
        $output = $tester->getDisplay();

        // Assert
        $this->assertRegExp('#' . $command->getName() . '#', $output);
    }

    /**
     * @return void
     */
    public function testExecuteGitPrByTheGitHasFinished(): void
    {
        // Arrange
        $upgradeCommand = new UpgradeCommand();
        $tester = $this->tester->getConsoleTester($upgradeCommand);
        $command = $this->getGitPrForGitCommand();

        // Act
        $tester->execute(
            [
                '--filter' => $command->getName(),
                '--pr_vendor' => $command->getVendor(),
            ],
        );
        $output = $tester->getDisplay();

        // Assert
        $this->assertRegExp('#' . $command->getName() . '#', $output);
    }

    /**
     * @return void
     */
    public function testExecuteGitPrByTheBitbucketHasFinished(): void
    {
        // Arrange
        $upgradeCommand = new UpgradeCommand();
        $tester = $this->tester->getConsoleTester($upgradeCommand);
        $command = $this->getGitPrForBitbucketCommand();

        // Act
        $tester->execute(
            [
                '--filter' => $command->getName(),
                '--pr_vendor' => $command->getVendor(),
            ],
        );
        $output = $tester->getDisplay();

        // Assert
        $this->assertRegExp('#' . $command->getName() . '#', $output);
    }
}
