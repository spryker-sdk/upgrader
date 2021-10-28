<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem;

use Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection;
use Upgrader\Business\VersionControlSystem\Response\VcsResponse;

interface VcsInterface
{
    /**
     * @var string
     */
    public const BRANCH_TEMPLATE = 'upgradebot/upgrade-for-%s-%s';

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function addChanges(): VcsResponse;

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function createBranch(string $branch): VcsResponse;

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function deleteLocalBranch(string $branch): VcsResponse;

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function deleteRemoteBranch(string $branch): VcsResponse;

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function checkout(): VcsResponse;

    /**
     * @param string $message
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function commitChanges(string $message): VcsResponse;

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function pushChanges(string $branch): VcsResponse;

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function checkUncommittedChanges(): VcsResponse;

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function checkTargetBranchExists(): VcsResponse;

    /**
     * @return bool
     */
    public function hasUncommittedChanges(): bool;

    /**
     * @param array<string> $releaseGroups
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function createPullRequest(array $releaseGroups): VcsResponse;

    /**
     * @param array<string> $releaseGroups
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection
     */
    public function save(array $releaseGroups): VcsResponseCollection;

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection
     */
    public function rollback(): VcsResponseCollection;

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\Collection\VcsResponseCollection
     */
    public function revertUncommittedChanges(): VcsResponseCollection;

    /**
     * @return string
     */
    public function getCommitHash(): string;

    /**
     * @return string
     */
    public function getHeadBranch(): string;

    /**
     * @return string
     */
    public function getBaseBranch(): string;
}
