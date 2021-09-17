<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\Command\Response\Collection;

use Upgrader\Business\Command\Response\CommandResponse;

class CommandResponseCollection
{
    /**
     * @var \Upgrader\Business\Command\Response\CommandResponse[]
     */
    protected $responseList = [];

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->isSuccess() ? CommandResponse::CODE_SUCCESS : CommandResponse::CODE_ERROR;
    }

    /**
     * @return \Upgrader\Business\Command\Response\CommandResponse[]
     */
    public function getResponseList(): array
    {
        return $this->responseList;
    }

    /**
     * @param \Upgrader\Business\Command\Response\CommandResponse $commandResponse
     *
     * @return void
     */
    public function add(CommandResponse $commandResponse): void
    {
        $this->responseList[] = $commandResponse;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        foreach ($this->getResponseList() as $result) {
            if (!$result->isSuccess()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        $messageList = [];
        foreach ($this->getResponseList() as $result) {
            $messageList[] = $result->getOutput();
        }

        return implode(PHP_EOL, $messageList);
    }
}
