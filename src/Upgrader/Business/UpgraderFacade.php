<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;

class UpgraderFacade implements UpgraderFacadeInterface
{
    /**
     * @var \Upgrader\Business\UpgraderBusinessFactory
     */
    protected $factory;

    /**
     * Specification:
     * -
     *
     * @api
     *
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function upgrade(): CommandResultOutput
    {
        return $this->getFactory()->createUpgrader()->upgrade();
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
