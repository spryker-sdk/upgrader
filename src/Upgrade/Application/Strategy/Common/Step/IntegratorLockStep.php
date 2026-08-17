<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Adapter\IntegratorExecutorInterface;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\RollbackStepInterface;

class IntegratorLockStep extends AbstractStep implements RollbackStepInterface
{
    protected IntegratorExecutorInterface $integratorClient;

    protected ConfigurationProviderInterface $configurationProvider;

    public function __construct(
        VersionControlSystemAdapterInterface $versionControlSystem,
        IntegratorExecutorInterface $integratorClient,
        ConfigurationProviderInterface $configurationProvider
    ) {
        parent::__construct($versionControlSystem);

        $this->integratorClient = $integratorClient;
        $this->configurationProvider = $configurationProvider;
    }

    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if (!$this->configurationProvider->isIntegratorEnabled()) {
            return $stepsExecutionDto;
        }

        return $this->integratorClient->runIntegratorLockUpdater($stepsExecutionDto);
    }

    public function rollBack(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->vsc->restore($stepsExecutionDto);
    }
}
