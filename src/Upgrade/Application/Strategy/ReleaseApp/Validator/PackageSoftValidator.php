<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Application\Exception\UpgraderException;
use Upgrade\Domain\Entity\Package;

class PackageSoftValidator implements PackageSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\ReleaseApp\Validator\Package\PackageValidatorInterface>
     */
    protected array $validatorList = [];

    /**
     * @param array<\Upgrade\Application\Strategy\ReleaseApp\Validator\Package\PackageValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function isValidPackage(Package $package): ResponseDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($package);
            }
        } catch (UpgraderException $exception) {
            return new ResponseDto(false, $exception->getMessage());
        }

        return new ResponseDto(true);
    }
}
