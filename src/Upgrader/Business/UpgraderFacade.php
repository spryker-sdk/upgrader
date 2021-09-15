<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Command\ResultOutput\Collection\CommandResultOutputCollection;

class UpgraderFacade implements UpgraderFacadeInterface
{
    /**
     * @var \Upgrader\Business\UpgraderBusinessFactory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function upgrade(): CommandResultOutputCollection
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
