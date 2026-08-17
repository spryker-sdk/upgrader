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
    public function isRemoteTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function isLocalTargetBranchNotExist(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function hasAnyUncommittedChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function createBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function addChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function commitChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function pushChanges(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function createPullRequest(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function checkout(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function deleteLocalBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function deleteRemoteBranch(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function restore(StepsResponseDto $stepsExecutionDto): StepsResponseDto;

    public function validateSourceCodeProviderCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto;
}
