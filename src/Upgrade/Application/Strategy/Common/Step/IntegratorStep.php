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
use Upgrade\Application\Strategy\Common\IntegratorExecutionValidatorInterface;
use Upgrade\Application\Strategy\RollbackStepInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class IntegratorStep extends AbstractStep implements RollbackStepInterface
{
    /**
     * @var \Upgrade\Application\Adapter\IntegratorExecutorInterface
     */
    protected IntegratorExecutorInterface $integratorClient;

    /**
     * @var \Upgrade\Application\Strategy\Common\IntegratorExecutionValidatorInterface
     */
    protected IntegratorExecutionValidatorInterface $integratorExecutorValidator;

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $versionControlSystem
     * @param \Upgrade\Application\Adapter\IntegratorExecutorInterface $integratorClient
     * @param \Upgrade\Application\Strategy\Common\IntegratorExecutionValidatorInterface $integratorExecutorValidator
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(
        VersionControlSystemAdapterInterface $versionControlSystem,
        IntegratorExecutorInterface $integratorClient,
        IntegratorExecutionValidatorInterface $integratorExecutorValidator,
        ConfigurationProvider $configurationProvider
    ) {
        parent::__construct($versionControlSystem);

        $this->integratorClient = $integratorClient;
        $this->integratorExecutorValidator = $integratorExecutorValidator;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if (!$this->integratorExecutorValidator->isIntegratorShouldBeInvoked()) {
            return $stepsExecutionDto;
        }

        $releaseGroupId = $this->configurationProvider->getReleaseGroupId();

        return $this->integratorClient->runIntegrator(
            $stepsExecutionDto,
            $releaseGroupId !== null && $stepsExecutionDto->getLastAppliedReleaseGroup()
                ? $stepsExecutionDto->getLastAppliedReleaseGroup()->getModuleCollection()->toArray()
                : [],
        );
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function rollBack(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->vsc->restore($stepsExecutionDto);
    }
}
