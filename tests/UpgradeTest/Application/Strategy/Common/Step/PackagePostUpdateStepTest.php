<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common\Step;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\PackagePostUpdateHandler\HandlerInterface;
use Upgrade\Application\Strategy\Common\Step\PackagePostUpdateStep;

class PackagePostUpdateStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testRun(): void
    {
        // Arrange
        $handlerFailed = $this->createMock(HandlerInterface::class);
        $handlerFailed->method('isApplicable')->willReturn(true);
        $stepsResponseDto = new StepsResponseDto(false, 'Error message');
        $handlerFailed->method('handle')->willReturn($stepsResponseDto);

        $handlerSuccess = $this->createMock(HandlerInterface::class);
        $handlerSuccess->method('isApplicable')->willReturn(false);

        $step = new PackagePostUpdateStep([$handlerFailed, $handlerSuccess]);

        // Act
        $result = $step->run(new StepsResponseDto(true));

        // Assert
        $this->assertInstanceOf(StepsResponseDto::class, $result);
        $this->assertFalse($result->isSuccessful());
    }
}
