<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageDto;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use Upgrade\Infrastructure\Exception\UpgraderException;

class PackageSoftValidator implements PackageSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\Package\PackageValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<\Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\Package\PackageValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \PackageManager\Domain\Dto\PackageDto $package
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function isValidPackage(PackageDto $package): PackageManagerResponseDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($package);
            }
        } catch (UpgraderException $exception) {
            return new PackageManagerResponseDto(false, $exception->getMessage());
        }

        return new PackageManagerResponseDto(true);
    }
}
