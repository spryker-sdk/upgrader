<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Validator;

use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Application\Exception\UpgraderException;

class ProjectValidator implements ProjectValidatorInterface
{
    /**
     * @var iterable<\Upgrade\Application\Validator\ProjectValidatorRuleInterface>
     */
    protected iterable $projectValidators;

    /**
     * @param iterable<\Upgrade\Application\Validator\ProjectValidatorRuleInterface> $projectValidators
     */
    public function __construct(iterable $projectValidators)
    {
        $this->projectValidators = $projectValidators;
    }

    /**
     * @return array<\Upgrade\Application\Dto\ValidatorViolationDto>
     */
    public function validateProject(): array
    {
        $violations = [];

        foreach ($this->projectValidators as $validator) {
            try {
                $validator->validate();
            } catch (UpgraderException $e) {
                $violations[] = new ValidatorViolationDto($validator->getViolationTitle(), $e->getMessage());
            }
        }

        return $violations;
    }
}
