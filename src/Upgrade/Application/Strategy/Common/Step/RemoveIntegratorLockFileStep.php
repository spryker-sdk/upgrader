<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use SprykerSdk\Utils\Infrastructure\Service\Filesystem;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\StepInterface;

class RemoveIntegratorLockFileStep extends AbstractStep implements StepInterface
{
    /**
     * @var string
     */
    protected const FILENAME = 'integrator.lock';

    /**
     * @var \SprykerSdk\Utils\Infrastructure\Service\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $versionControlSystem
     * @param \SprykerSdk\Utils\Infrastructure\Service\Filesystem $filesystem
     */
    public function __construct(
        VersionControlSystemAdapterInterface $versionControlSystem,
        Filesystem $filesystem
    ) {
        parent::__construct($versionControlSystem);

        $this->filesystem = $filesystem;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if ($this->filesystem->exists(static::FILENAME)) {
            $this->filesystem->remove(static::FILENAME);
        }

        if ($this->vsc->hasUncommittedFile(static::FILENAME)) {
            $this->vsc->removeTrackedFiles(static::FILENAME);
            $this->vsc->commitWithMessage('Remove ' . static::FILENAME . ' file');
        }

        return $stepsExecutionDto;
    }
}
