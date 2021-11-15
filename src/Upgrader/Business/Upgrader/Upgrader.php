<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Exception\UpgraderFlowException;
use Upgrader\Business\Upgrader\Request\UpgraderRequest;
use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;
use Upgrader\Business\Upgrader\Response\UpgraderResponse;
use Upgrader\Business\Upgrader\Strategy\UpdateStrategyGeneratorInterface;
use Upgrader\Business\VersionControlSystem\VcsInterface;

class Upgrader implements UpgraderInterface
{
    /**
     * @var string
     */
    protected const NOTHING_TO_UPDATE_OUTPUT_MESSAGE = 'The branch is up to date. No further action is required.';

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

        try {
            $responses->addCollection($this->check());
            $upgradeResponses = $this->performUpdate($request);
            $responses->addCollection($upgradeResponses);
            $responses->addCollection($this->storeResults($upgradeResponses));
        } catch (UpgraderFlowException $exception) {
            $responses->addCollection($exception->getResponses());
        }

        return $responses;
    }

    /**
     * @throws \Upgrader\Business\Exception\UpgraderFlowException
     *
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    protected function check(): UpgraderResponseCollection
    {
        $responses = new UpgraderResponseCollection();

        $checkResponse = $this->vcs->checkTargetBranchExists();
        $responses->add($checkResponse);
        if (!$checkResponse->isSuccess()) {
            throw new UpgraderFlowException($responses);
        }
        $checkResponse = $this->vcs->checkUncommittedChanges();
        $responses->add($checkResponse);
        if (!$checkResponse->isSuccess()) {
            throw new UpgraderFlowException($responses);
        }

        return $responses;
    }

    /**
     * @param \Upgrader\Business\Upgrader\Request\UpgraderRequest $request
     *
     * @throws \Upgrader\Business\Exception\UpgraderFlowException
     *
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    protected function performUpdate(UpgraderRequest $request): UpgraderResponseCollection
    {
        $responses = new UpgraderResponseCollection();

        $upgradeResponses = $this->updateStrategyGenerator->getStrategy($request)->upgrade();
        if ($upgradeResponses->isEmpty()) {
            $response = new UpgraderResponse(true, self::NOTHING_TO_UPDATE_OUTPUT_MESSAGE);
            $responses->add($response);

            throw new UpgraderFlowException($responses);
        }
        $responses->addCollection($upgradeResponses);
        if (!$upgradeResponses->hasSuccessfulResponse()) {
            throw new UpgraderFlowException($responses);
        }

        return $responses;
    }

    /**
     * @param \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection $upgradeResponses
     *
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    protected function storeResults(UpgraderResponseCollection $upgradeResponses): UpgraderResponseCollection
    {
        $responses = new UpgraderResponseCollection();

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
