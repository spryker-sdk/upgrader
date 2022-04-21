<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator;

use Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;
use Upgrade\Infrastructure\Exception\UpgraderException;

class ReleaseGroupSoftValidator implements ReleaseGroupSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<\Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
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
