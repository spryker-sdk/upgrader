<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Provider;

use Upgrader\Business\VersionControlSystem\Response\VcsResponse;

interface ProviderInterface
{
    /**
     * @param array $params
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function createPullRequest(array $params): VcsResponse;
}
