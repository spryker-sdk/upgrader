<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Composer\Step;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\Step\AbstractStep;
use Upgrade\Application\Strategy\RollbackStepInterface;
use Upgrade\Domain\ValueObject\Error;

class ComposerUpdateStep extends AbstractStep implements RollbackStepInterface
{
    protected PackageManagerAdapterInterface $packageManager;

    public function __construct(
        VersionControlSystemAdapterInterface $versionControlSystem,
        PackageManagerAdapterInterface $packageManager
    ) {
        parent::__construct($versionControlSystem);

        $this->packageManager = $packageManager;
    }

    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $updateResponse = $this->packageManager->update();
        $stepsExecutionDto->setIsSuccessful($updateResponse->isSuccessful());
        if (!$updateResponse->isSuccessful()) {
            $stepsExecutionDto->setError(
                Error::createClientCodeError($updateResponse->getOutputMessage() ?? 'Composer update error'),
            );
        }

        return $stepsExecutionDto;
    }

    public function rollBack(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->vsc->restore($stepsExecutionDto);
    }
}
