<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator;

use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;

class ReleaseGroupSoftValidator implements ReleaseGroupSoftValidatorInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface[]
     */
    protected $validatorList = [];

    /**
     * @param \Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface[] $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer $releaseGroup
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function isValidReleaseGroup(ReleaseGroupTransfer $releaseGroup): CommandResponse
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($releaseGroup);
            }
        } catch (UpgraderException $exception) {
            return new CommandResponse(false, $exception->getMessage());
        }

        return new CommandResponse(true);
    }
}
