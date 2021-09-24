<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;

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
     * @param \Upgrader\Business\Command\CommandRequest $commandRequest
     *
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function upgrade(CommandRequest $commandRequest): CommandResponseCollection
    {
        return $this->getFactory()->createCommandExecutor()->run($commandRequest);
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

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Upgrader\Business\Command\CommandInterface[]
     */
    public function getUpgraderCommands(): array
    {
        return $this->getFactory()->createCommandExecutor()->getCommands();
    }
}
