<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Adapter;

use Upgrade\Application\Dto\StepsResponseDto;

interface VersionControlSystemAdapterInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function isRemoteTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function isLocalTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function hasAnyUncommittedChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function createBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function addChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function commitChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function pushChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function createPullRequest(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function checkout(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function deleteLocalBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function deleteRemoteBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function restore(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function validateSourceCodeProviderCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function findChangedFiles(StepsResponseDto $stepsExecutionDto): StepsResponseDto;
}
