<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\Event;

use Upgrade\Application\Dto\StepsResponseDto;

class ReleaseGroupProcessorEvent
{
    /**
     * @var string
     */
    public const PRE_PROCESSOR = 'PRE_PROCESSOR';

    /**
     * @var string
     */
    public const POST_PROCESSOR = 'POST_PROCESSOR';

    /**
     * @var string
     */
    public const PRE_REQUIRE = 'PRE_REQUIRE';

    /**
     * @var string
     */
    public const POST_REQUIRE = 'POST_REQUIRE';

    /**
     * @var \Upgrade\Application\Dto\StepsResponseDto
     */
    protected StepsResponseDto $stepsExecutionDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     */
    public function __construct(StepsResponseDto $stepsExecutionDto)
    {
        $this->stepsExecutionDto = $stepsExecutionDto;
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function getStepsExecutionDto(): StepsResponseDto
    {
        return $this->stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return void
     */
    public function setStepsExecutionDto(StepsResponseDto $stepsExecutionDto): void
    {
        $this->stepsExecutionDto = $stepsExecutionDto;
    }
}
