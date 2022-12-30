<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Adapter\CodeStyleFixerAdapterInterface;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\StepInterface;

class CodeStyleFixerStep extends AbstractStep implements StepInterface
{
    /**
     * @var \Upgrade\Application\Adapter\CodeStyleFixerAdapterInterface
     */
    protected CodeStyleFixerAdapterInterface $codeFixerAdapter;

    /**
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $versionControlSystem
     * @param \Upgrade\Application\Adapter\CodeStyleFixerAdapterInterface $codeFixerAdapter
     */
    public function __construct(VersionControlSystemAdapterInterface $versionControlSystem, CodeStyleFixerAdapterInterface $codeFixerAdapter)
    {
        parent::__construct($versionControlSystem);

        $this->codeFixerAdapter = $codeFixerAdapter;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->codeFixerAdapter->runCodeFixer($stepsExecutionDto);
    }
}
