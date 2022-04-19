<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator;

use Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto;
use Upgrade\Infrastructure\Exception\UpgraderException;

class ReleaseGroupSoftValidator implements ReleaseGroupSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<\Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): PackageManagerResponseDtoDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($releaseGroup);
            }
        } catch (UpgraderException $exception) {
            return new PackageManagerResponseDtoDto(false, $exception->getMessage());
        }

        return new PackageManagerResponseDtoDto(true);
    }
}
