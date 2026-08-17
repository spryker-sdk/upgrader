<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Git\Adapter;

use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Infrastructure\VersionControlSystem\Git\Git;

/**
 * TODO :: Get rid of this class. It just duplicates all the logic in the Git class and adds no value.
 */
class GitAdapter implements VersionControlSystemAdapterInterface
{
    protected Git $git;

    public function __construct(Git $git)
    {
        $this->git = $git;
    }

    public function isRemoteTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->isRemoteTargetBranchNotExist($stepsExecutionDto);
    }

    public function isLocalTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->isLocalTargetBranchNotExist($stepsExecutionDto);
    }

    public function hasAnyUncommittedChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->hasAnyUncommittedChanges($stepsExecutionDto);
    }

    public function createBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->createBranch($stepsExecutionDto);
    }

    public function addChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->add($stepsExecutionDto);
    }

    public function commitChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->commit($stepsExecutionDto);
    }

    public function pushChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->push($stepsExecutionDto);
    }

    public function createPullRequest(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->createPullRequest($stepsExecutionDto);
    }

    public function checkout(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->checkout($stepsExecutionDto);
    }

    public function deleteLocalBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->deleteLocalBranch($stepsExecutionDto);
    }

    public function deleteRemoteBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->deleteRemoteBranch($stepsExecutionDto);
    }

    public function restore(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->restore($stepsExecutionDto);
    }

    public function validateSourceCodeProviderCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->validateSourceCodeProviderCredentials($stepsExecutionDto);
    }
}
