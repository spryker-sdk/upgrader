<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Exception\UpgraderException;
use Upgrade\Application\Dto\ExecutionDto;

class ReleaseGroupSoftValidator implements ReleaseGroupSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<\Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param ReleaseGroupDto $releaseGroup
     * @return \Upgrade\Domain\Entity\\Upgrade\Application\Dto\ExecutionDto
     */
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): ExecutionDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($releaseGroup);
            }
        } catch (UpgraderException $exception) {
            return new ExecutionDto(false, $exception->getMessage());
        }

        return new ExecutionDto(true);
    }
}
