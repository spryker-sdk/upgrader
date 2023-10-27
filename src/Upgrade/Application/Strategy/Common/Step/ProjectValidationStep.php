<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\StepInterface;
use Upgrade\Application\Validator\ProjectValidatorInterface;

class ProjectValidationStep implements StepInterface
{
    /**
     * @var \Upgrade\Application\Validator\ProjectValidatorInterface
     */
    protected ProjectValidatorInterface $projectSoftValidator;

    /**
     * @param \Upgrade\Application\Validator\ProjectValidatorInterface $projectSoftValidator
     */
    public function __construct(ProjectValidatorInterface $projectSoftValidator)
    {
        $this->projectSoftValidator = $projectSoftValidator;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $projectViolations = $this->projectSoftValidator->validateProject();

        foreach ($projectViolations as $violation) {
            $stepsExecutionDto->addProjectViolation($violation);
        }

        return $stepsExecutionDto;
    }
}
