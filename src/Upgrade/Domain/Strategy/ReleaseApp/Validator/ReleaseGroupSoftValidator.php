<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;
use Upgrade\Infrastructure\Exception\UpgraderException;

class ReleaseGroupSoftValidator implements ReleaseGroupSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<\Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \ReleaseAppClient\Domain\Dto\ReleaseGroupDto $releaseGroup
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): PackageManagerResponseDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($releaseGroup);
            }
        } catch (UpgraderException $exception) {
            return new PackageManagerResponseDto(false, $exception->getMessage());
        }

        return new PackageManagerResponseDto(true);
    }
}
