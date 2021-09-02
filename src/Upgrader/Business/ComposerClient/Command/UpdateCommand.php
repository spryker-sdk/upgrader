<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\ComposerClient\Command;

use Upgrader\Business\Command\AbstractCommand;

class UpdateCommand extends AbstractCommand
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'composer update';
    }
}
