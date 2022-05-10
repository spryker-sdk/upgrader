<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use Upgrade\Domain\Entity\Package;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use Upgrade\Application\Exception\UpgraderException;
use Upgrade\Application\Dto\ExecutionDto;

class PackageSoftValidator implements PackageSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\ReleaseApp\Validator\Package\PackageValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<\Upgrade\Application\Strategy\ReleaseApp\Validator\Package\PackageValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     * @return \Upgrade\Domain\Entity\\Upgrade\Domain\Entity\Step\\Upgrade\Application\Dto\ExecutionDto
     */
    public function isValidPackage(Package $package): ExecutionDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($package);
            }
        } catch (UpgraderException $exception) {
            return new ExecutionDto(false, $exception->getMessage());
        }

        return new ExecutionDto(true);
    }
}
