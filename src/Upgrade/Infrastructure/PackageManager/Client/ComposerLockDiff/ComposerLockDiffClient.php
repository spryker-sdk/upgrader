<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Client\ComposerLockDiff;

use Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection;
use Upgrade\Application\Dto\PackageManager\ComposerLockDiffResponseDto;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;
use Upgrade\Infrastructure\PackageManager\Client\ComposerLockDiffClientInterface;

class ComposerLockDiffClient implements ComposerLockDiffClientInterface
{
    /**
     * @var \Upgrade\Infrastructure\PackageManager\Client\ComposerLockDiff\ComposerLockDiffCallExecutorInterface
     */
    protected $composerLockDiffCallExecutor;

    /**
     * @param \Upgrade\Infrastructure\PackageManager\Client\ComposerLockDiff\ComposerLockDiffCallExecutorInterface $composerLockDiffCallExecutor
     */
    public function __construct(ComposerLockDiffCallExecutorInterface $composerLockDiffCallExecutor)
    {
        $this->composerLockDiffCallExecutor = $composerLockDiffCallExecutor;
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection
     */
    public function getComposerLockDiff(): PackageManagerResponseDtoCollection
    {
        $response = $this->composerLockDiffCallExecutor->getComposerLockDiff();

        if ($response->isSuccess()) {
            $bodyArray = json_decode((string)$response->getRawOutput(), true);
            $responseData = new ComposerLockDiffResponseDto($bodyArray);

            if ($responseData->isEmpty()) {
                return new PackageManagerResponseDtoCollection();
            }

            return new PackageManagerResponseDtoCollection([
                $this->createSuccessResponse($response->getRawOutput(), $responseData->getChanges()->toArray()),
                $this->createSuccessResponse($response->getRawOutput(), $responseData->getChangesDev()->toArray()),
            ]);
        }

        return new PackageManagerResponseDtoCollection([$response]);
    }

    /**
     * @param string|null $output
     * @param array $packageList
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    protected function createSuccessResponse(?string $output, array $packageList): PackageManagerResponseDto
    {
        return new PackageManagerResponseDto(true, $output, $packageList);
    }
}
