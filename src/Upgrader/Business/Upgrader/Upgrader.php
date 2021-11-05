<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Upgrader\Request\UpgraderRequest;
use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;
use Upgrader\Business\Upgrader\Strategy\UpdateStrategyGeneratorInterface;
use Upgrader\Business\VersionControlSystem\VcsInterface;

class Upgrader implements UpgraderInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Strategy\UpdateStrategyGeneratorInterface
     */
    protected $updateStrategyGenerator;

    /**
     * @var \Upgrader\Business\VersionControlSystem\VcsInterface
     */
    protected $vcs;

    /**
     * @param \Upgrader\Business\Upgrader\Strategy\UpdateStrategyGeneratorInterface $updateStrategyGenerator
     * @param \Upgrader\Business\VersionControlSystem\VcsInterface $vcs
     */
    public function __construct(UpdateStrategyGeneratorInterface $updateStrategyGenerator, VcsInterface $vcs)
    {
        $this->updateStrategyGenerator = $updateStrategyGenerator;
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

        $upgradeResponses = $this->updateStrategyGenerator->getStrategy($request)->upgrade();

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
