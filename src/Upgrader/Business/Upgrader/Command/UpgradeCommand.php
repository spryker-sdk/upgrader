<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Command;

use Exception;
use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\Upgrader\UpgraderInterface;

class UpgradeCommand implements CommandInterface
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'upgrader upgrade';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'upgrader:upgrade';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'The command for upgrade Spryker packages';
    }

    /**
     * @var \Upgrader\Business\Upgrader\UpgraderInterface
     */
    protected $upgrader;

    /**
     * @param \Upgrader\Business\Upgrader\UpgraderInterface $upgrader
     */
    public function __construct(UpgraderInterface $upgrader)
    {
        $this->upgrader = $upgrader;
    }

    /**
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function run(): CommandResponse
    {
        try {
            $requireResponseCollection = $this->upgrader->upgrade();
        } catch (Exception $exception) {
            return $this->createResponse(false, $exception->getMessage());
        }

        return $this->createResponse(true, $requireResponseCollection->getOutput());
    }

    /**
     * @param bool $isSuccess
     * @param string $message
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    protected function createResponse(bool $isSuccess, string $message): CommandResponse
    {
        return new CommandResponse($isSuccess, $this->getName(), $message);
    }
}
