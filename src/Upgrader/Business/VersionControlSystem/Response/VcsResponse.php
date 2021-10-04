<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Response;

use Upgrader\Business\Upgrader\Response\AbstractUpgraderResponse;

class VcsResponse extends AbstractUpgraderResponse
{
    /**
     * @return string|null
     */
    public function getOutput(): ?string
    {
        return 'VCS: ' . $this->output;
    }
}