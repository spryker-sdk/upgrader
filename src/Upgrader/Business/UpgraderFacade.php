<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Upgrader\Request\UpgraderRequest;
use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;

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
     * @param \Upgrader\Business\Upgrader\Request\UpgraderRequest $request
     *
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    public function upgrade(UpgraderRequest $request): UpgraderResponseCollection
    {
        return $this->getFactory()->createUpgrader()->upgrade($request);
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
