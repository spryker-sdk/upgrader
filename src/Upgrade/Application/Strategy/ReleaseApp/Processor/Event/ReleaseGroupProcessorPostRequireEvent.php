<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\Event;

use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;

class ReleaseGroupProcessorPostRequireEvent extends ReleaseGroupProcessorEvent
{
    /**
     * @var string
     */
    public const POST_REQUIRE = 'POST_REQUIRE';

    /**
     * @var \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    protected PackageManagerResponseDto $packageManagerResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     * @param \Upgrade\Application\Dto\PackageManagerResponseDto $packageManagerResponseDto
     */
    public function __construct(StepsResponseDto $stepsExecutionDto, PackageManagerResponseDto $packageManagerResponseDto)
    {
        parent::__construct($stepsExecutionDto);
        $this->packageManagerResponseDto = $packageManagerResponseDto;
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function getPackageManagerResponseDto(): PackageManagerResponseDto
    {
        return $this->packageManagerResponseDto;
    }
}
