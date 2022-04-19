<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator;

use Upgrade\Application\Dto\PackageManager\PackageDto;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto;
use Upgrade\Infrastructure\Exception\UpgraderException;

class PackageSoftValidator implements PackageSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\Package\PackageValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<\Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\Package\PackageValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\PackageDto $package
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function isValidPackage(PackageDto $package): PackageManagerResponseDtoDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($package);
            }
        } catch (UpgraderException $exception) {
            return new PackageManagerResponseDtoDto(false, $exception->getMessage());
        }

        return new PackageManagerResponseDtoDto(true);
    }
}
