<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\ComposerLockDiff;

use Upgrader\Business\PackageManager\Client\ComposerLockDiff\Response\ComposerLockDiffResponse;
use Upgrader\Business\PackageManager\Client\ComposerLockDiffClientInterface;
use Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;

class ComposerLockDiffClient implements ComposerLockDiffClientInterface
{
    /**
     * @var \Upgrader\Business\PackageManager\Client\ComposerLockDiff\ComposerLockDiffCallExecutorInterface
     */
    protected $composerLockDiffCallExecutor;

    /**
     * @param \Upgrader\Business\PackageManager\Client\ComposerLockDiff\ComposerLockDiffCallExecutorInterface $composerLockDiffCallExecutor
     */
    public function __construct(ComposerLockDiffCallExecutorInterface $composerLockDiffCallExecutor)
    {
        $this->composerLockDiffCallExecutor = $composerLockDiffCallExecutor;
    }

    /**
     * @return \Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection
     */
    public function getComposerLockDiff(): PackageManagerResponseCollection
    {
        $rawResponse = $this->composerLockDiffCallExecutor->getComposerLockDiff();

        if ($rawResponse->isSuccess()) {
            $bodyArray = json_decode((string)$rawResponse->getRawOutput(), true);
            $responseData = new ComposerLockDiffResponse($bodyArray);

            return new PackageManagerResponseCollection([
                $this->createSuccessResponse($rawResponse->getRawOutput(), $responseData->getChanges()->toArray()),
                $this->createSuccessResponse($rawResponse->getRawOutput(), $responseData->getChangesDev()->toArray()),
            ]);
        }

        return new PackageManagerResponseCollection([$rawResponse]);
    }

    /**
     * @param string|null $output
     * @param array $packageList
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    protected function createSuccessResponse(?string $output, array $packageList): PackageManagerResponse
    {
        return new PackageManagerResponse(true, $output, $packageList);
    }
}
