<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider;

use Upgrade\Infrastructure\Dto\SourceCodeProvider\PullRequestDto;
use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;

interface SourceCodeProviderInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     * @param \Upgrade\Infrastructure\Dto\SourceCodeProvider\PullRequestDto $pullRequestDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function createPullRequest(StepsExecutionDto $stepsExecutionDto, PullRequestDto $pullRequestDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function validateCredentials(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;
}
