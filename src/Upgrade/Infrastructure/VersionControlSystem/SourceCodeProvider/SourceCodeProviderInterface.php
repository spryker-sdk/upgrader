<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;

interface SourceCodeProviderInterface
{
    public function getName(): string;

    public function createPullRequest(
        StepsResponseDto $stepsExecutionDto,
        PullRequestDto $pullRequestDto
    ): StepsResponseDto;

    public function validateCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function buildBlockerTextBlock(ValidatorViolationDto $blocker): string;
}
