<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider;

use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;

interface SourceCodeProviderInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     * @param \Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto $pullRequestDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function createPullRequest(
        StepsExecutionDto $stepsExecutionDto,
        PullRequestDto $pullRequestDto
    ): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function validateCredentials(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;
}
