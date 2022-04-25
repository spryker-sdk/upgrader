<?php

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;

interface ThresholdValidatorInterface
{
    /**
     * @param ReleaseGroupDtoCollection $releaseReleaseGroup
     * @return void
     */
    public function validate(ReleaseGroupDtoCollection $releaseReleaseGroup): void;
}
