<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator\ReleaseGroup;

use Upgrader\Business\DataProvider\Entity\ReleaseGroup;
use Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface;

class ReleaseGroupValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface[]
     */
    protected $ruleList;

    /**
     * @param \Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface[] $ruleList
     */
    public function __construct(array $ruleList)
    {
        $this->ruleList = $ruleList;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup
     *
     * @return void
     */
    public function validate(ReleaseGroup $releaseGroup): void
    {
        foreach ($this->ruleList as $rule) {
            $rule->validate($releaseGroup);
        }
    }
}
