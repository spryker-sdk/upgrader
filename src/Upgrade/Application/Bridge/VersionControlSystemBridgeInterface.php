<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Bridge;

use Upgrade\Application\Dto\StepsExecutionDto;

interface VersionControlSystemBridgeInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function isRemoteTargetBranchNotExist(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function isLocalTargetBranchNotExist(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function hasAnyUncommittedChanges(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function createBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function addChanges(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function commitChanges(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function pushChanges(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function createPullRequest(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function checkout(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function deleteLocalBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function deleteRemoteBranch(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function restore(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;
}
