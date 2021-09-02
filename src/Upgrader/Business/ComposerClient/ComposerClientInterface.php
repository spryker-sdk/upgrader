<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\ComposerClient;

use Upgrader\Business\Command\CommandResultInterface;

interface ComposerClientInterface
{
    /**
     * @return array
     */
    public function getComposerJsonBodyAsArray(): array;

    /**
     * @return array
     */
    public function getComposerLockBodyAsArray(): array;

    /**
     * @return \Upgrader\Business\Command\CommandResultInterface
     */
    public function runComposerUpdate(): CommandResultInterface;
}
