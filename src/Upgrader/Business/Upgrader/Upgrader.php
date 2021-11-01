<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Upgrader\Request\UpgraderRequest;
use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;
use Upgrader\Business\Upgrader\Strategy\ComposerUpdateStrategy;
use Upgrader\Business\Upgrader\Strategy\ReleaseGroupStrategy;
use Upgrader\Business\VersionControlSystem\VcsInterface;

class Upgrader implements UpgraderInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Strategy\ComposerUpdateStrategy
     */
    protected $composerUpdateStrategy;

    /**
     * @var \Upgrader\Business\Upgrader\Strategy\ReleaseGroupStrategy
     */
    protected $releaseGroupStrategy;

    /**
     * @var \Upgrader\Business\VersionControlSystem\VcsInterface
     */
    protected $vcs;

    /**
     * @param \Upgrader\Business\Upgrader\Strategy\ComposerUpdateStrategy $composerUpdateStrategy
     * @param \Upgrader\Business\Upgrader\Strategy\ReleaseGroupStrategy $releaseGroupStrategy
     * @param \Upgrader\Business\VersionControlSystem\VcsInterface $vcs
     */
    public function __construct(
        ComposerUpdateStrategy $composerUpdateStrategy,
        ReleaseGroupStrategy $releaseGroupStrategy,
        VcsInterface $vcs
    ) {
        $this->composerUpdateStrategy = $composerUpdateStrategy;
        $this->releaseGroupStrategy = $releaseGroupStrategy;
        $this->vcs = $vcs;
    }

    /**
     * @param \Upgrader\Business\Upgrader\Request\UpgraderRequest $request
     *
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    public function upgrade(UpgraderRequest $request): UpgraderResponseCollection
    {
        $responses = new UpgraderResponseCollection();

        $checkResponse = $this->vcs->checkTargetBranchExists();
        $responses->add($checkResponse);
        if (!$checkResponse->isSuccess()) {
            return $responses;
        }
        $checkResponse = $this->vcs->checkUncommittedChanges();
        $responses->add($checkResponse);
        if (!$checkResponse->isSuccess()) {
            return $responses;
        }

        if ($request->getStrategyEnum()->isComposerUpdate()) {
            $upgradeResponses = $this->composerUpdateStrategy->upgrade();
        } else {
            $upgradeResponses = $this->releaseGroupStrategy->upgrade();
        }

        $responses->addCollection($upgradeResponses);
        if (!$upgradeResponses->hasSuccessfulResponse()) {
            return $responses;
        }
        $vcsResponses = $this->vcs->save($upgradeResponses->getSuccessfulResults());
        $responses->addCollection($vcsResponses);
        $responses->add($this->vcs->checkout());
        if ($vcsResponses->hasResponseWithError()) {
            $rollbackResponses = $this->vcs->rollback();
            $responses->addCollection($rollbackResponses);
        }

        return $responses;
    }
}
