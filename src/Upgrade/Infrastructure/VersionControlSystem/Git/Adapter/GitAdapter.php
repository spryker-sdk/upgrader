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

class GitAdapter implements VersionControlSystemAdapterInterface
{
    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Git\Git
     */
    protected Git $git;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Git\Git $git
     */
    public function __construct(Git $git)
    {
        $this->git = $git;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function isRemoteTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->isRemoteTargetBranchNotExist($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function isLocalTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->isLocalTargetBranchNotExist($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function hasAnyUncommittedChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->hasAnyUncommittedChanges($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function createBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->createBranch($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function addChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->add($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function commitChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->commit($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function pushChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->push($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function createPullRequest(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->createPullRequest($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function checkout(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->checkout($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function deleteLocalBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->deleteLocalBranch($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function deleteRemoteBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->deleteRemoteBranch($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function restore(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->restore($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function validateSourceCodeProviderCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->validateSourceCodeProviderCredentials($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function findChangedFiles(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->git->findChangedFiles($stepsExecutionDto);
    }
}
