<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Infrastructure\Exception\UpgraderException;

class ThresholdSoftValidator implements ThresholdSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Domain\Strategy\ReleaseApp\Validator\Threshold\ThresholdValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<\Upgrade\Domain\Strategy\ReleaseApp\Validator\Threshold\ThresholdValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection $groupDtoCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function isWithInThreshold(ReleaseGroupDtoCollection $groupDtoCollection): PackageManagerResponseDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($groupDtoCollection);
            }
        } catch (UpgraderException $exception) {
            return new PackageManagerResponseDto(false, $exception->getMessage());
        }

        return new PackageManagerResponseDto(true);
    }
}
