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
    public function add(): VcsResponse;

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function branch(string $branch): VcsResponse;

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function checkout(string $branch): VcsResponse;

    /**
     * @param string $message
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function commit(string $message): VcsResponse;

    /**
     * @param string $branch
     *
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function push(string $branch): VcsResponse;

    /**
     * @return \Upgrader\Business\VersionControlSystem\Response\VcsResponse
     */
    public function check(): VcsResponse;

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
