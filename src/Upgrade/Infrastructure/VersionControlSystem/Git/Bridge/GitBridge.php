<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Git\Bridge;

use Upgrade\Application\Bridge\VersionControlSystemBridgeInterface;
use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Git\Git;

class GitBridge implements VersionControlSystemBridgeInterface
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
     * @return string
     */
    public function getType(): string
    {
        return ConfigurationProvider::VCS_TYPE;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function isRemoteTargetBranchNotExist(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->isRemoteTargetBranchNotExist($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function isLocalTargetBranchNotExist(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->isLocalTargetBranchNotExist($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function hasAnyUncommittedChanges(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->hasAnyUncommittedChanges($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function createBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->createBranch($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function addChanges(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->add($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function commitChanges(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->commit($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function pushChanges(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->push($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function createPullRequest(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->createPullRequest($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function checkout(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->checkout($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function deleteLocalBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->deleteLocalBranch($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function deleteRemoteBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->deleteRemoteBranch($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function restore(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->git->restore($stepsExecutionDto);
    }
}
