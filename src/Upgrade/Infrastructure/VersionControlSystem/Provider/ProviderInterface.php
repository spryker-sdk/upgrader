<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Provider;

use Upgrade\Application\Dto\Step\StepsExecutionDto;

interface ProviderInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     * @param array $params
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function createPullRequest(StepsExecutionDto $stepsExecutionDto, array $params): StepsExecutionDto;
}
