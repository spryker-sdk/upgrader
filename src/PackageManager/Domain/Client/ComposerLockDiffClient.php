<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Client;

use PackageManager\Domain\Client\ComposerLockDiff\ComposerLockDiffCommandExecutorInterface;
use PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection;
use PackageManager\Domain\Dto\ComposerLockDiffResponseDto;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use PackageManager\Domain\Client\ComposerLockDiffClientInterface;

class ComposerLockDiffClient implements ComposerLockDiffClientInterface
{
    /**
     * @var \PackageManager\Domain\Client\ComposerLockDiff\ComposerLockDiffCommandExecutorInterface
     */
    protected $composerLockDiffCallExecutor;

    /**
     * @param \PackageManager\Domain\Client\ComposerLockDiff\ComposerLockDiffCommandExecutorInterface $composerLockDiffCallExecutor
     */
    public function __construct(ComposerLockDiffCommandExecutorInterface $composerLockDiffCallExecutor)
    {
        $this->composerLockDiffCallExecutor = $composerLockDiffCallExecutor;
    }

    /**
     * @return \PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection
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
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    protected function createSuccessResponse(?string $output, array $packageList): PackageManagerResponseDto
    {
        return new PackageManagerResponseDto(true, $output, $packageList);
    }
}
