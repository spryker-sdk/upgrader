<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common\Step;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Application\Strategy\Common\Step\ProjectValidationStep;
use Upgrade\Application\Validator\ProjectValidatorInterface;

class ProjectValidationStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testRunShouldInvokeProjectValidator(): void
    {
        // Arrange
        $violation = new ValidatorViolationDto('title', 'message');

        $projectValidationStep = new ProjectValidationStep(
            $this->createProjectValidatorMock([$violation]),
        );

        // Act
        $stepResponse = $projectValidationStep->run(new StepsResponseDto());

        // Assert
        $this->assertSame([$violation], $stepResponse->getProjectViolations());
    }

    /**
     * @param array<\Upgrade\Application\Dto\ValidatorViolationDto> $violations
     *
     * @return \Upgrade\Application\Validator\ProjectValidatorInterface
     */
    protected function createProjectValidatorMock(array $violations): ProjectValidatorInterface
    {
        $projectValidator = $this->createMock(ProjectValidatorInterface::class);
        $projectValidator->method('validateProject')->willReturn($violations);

        return $projectValidator;
    }
}
