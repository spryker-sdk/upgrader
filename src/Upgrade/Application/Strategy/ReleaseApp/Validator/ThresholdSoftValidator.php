<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\ExecutionDto;
use Upgrade\Application\Exception\UpgraderException;

class ThresholdSoftValidator implements ThresholdSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\ThresholdValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<\Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold\ThresholdValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $groupDtoCollection
     *
     * @return \Upgrade\Application\Dto\ExecutionDto
     */
    public function isWithInThreshold(ReleaseGroupDtoCollection $groupDtoCollection): ExecutionDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($groupDtoCollection);
            }
        } catch (UpgraderException $exception) {
            return new ExecutionDto(false, $exception->getMessage());
        }

        return new ExecutionDto(true);
    }
}
