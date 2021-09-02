<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Exception;
use Upgrader\Business\UpgraderBusinessFactory;

class Upgrader
{
    /**
     * @var \Upgrader\Business\UpgraderBusinessFactory
     */
    protected $factory;

    /**
     * @return \Upgrader\Business\Upgrader\UpgraderResultInterface
     */
    public function upgrade(): UpgraderResultInterface
    {
        try {
            $updateResult = $this->getFactory()->createComposerClient()->runComposerUpdate();
        } catch (Exception $exception) {
            return new UpgraderResult(false, $exception->getMessage());
        }

        return new UpgraderResult($updateResult->isSuccess(), $updateResult->getResultString());
    }

    /**
     * @return \Upgrader\Business\Upgrader\UpgraderResultInterface
     */
    public function isUpgradeAvailable(): UpgraderResultInterface
    {
        try {
            $hasUncommittedChanges = $this->getFactory()->createGitClient()->isUncommittedChangesExist();
        } catch (Exception $exception) {
            return new UpgraderResult(false, $exception->getMessage());
        }
        if ($hasUncommittedChanges) {
            return new UpgraderResult(false, 'Please commit or revert your changes');
        } else {
            return new UpgraderResult(true);
        }
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
