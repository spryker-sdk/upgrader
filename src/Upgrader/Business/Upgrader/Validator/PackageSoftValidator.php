<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator;

use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\PackageManager\Transfer\PackageTransfer;

class PackageSoftValidator implements PackageSoftValidatorInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Validator\Package\PackageValidatorInterface[]
     */
    protected $validatorList = [];

    /**
     * @param \Upgrader\Business\Upgrader\Validator\Package\PackageValidatorInterface[] $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\PackageTransfer $package
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function isValidPackage(PackageTransfer $package): CommandResponse
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($package);
            }
        } catch (UpgraderException $exception) {
            return new CommandResponse(false, $exception->getMessage());
        }

        return new CommandResponse(true);
    }
}
