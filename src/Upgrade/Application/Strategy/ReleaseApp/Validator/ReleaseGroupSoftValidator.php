<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Application\Exception\UpgraderException;

class ReleaseGroupSoftValidator implements ReleaseGroupSoftValidatorInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface>
     */
    protected array $validatorList = [];

    /**
     * @param array<\Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup\ReleaseGroupValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): ResponseDto
    {
        $violations = [];

        foreach ($this->validatorList as $validator) {
            try {
                $validator->validate($releaseGroup);
            } catch (UpgraderException $exception) {
                $violations[] = $exception->getMessage();
            }
        }

        return count($violations) > 0
            ? new ResponseDto(false, implode(PHP_EOL, $violations))
            : new ResponseDto(true);
    }
}
