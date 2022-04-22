<?php

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\Threshold;

use Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto;

interface ThresholdValidatorInterface
{
    /**
     * @param ReleaseGroupDtoCollection $releaseReleaseGroup
     * @return void
     */
    public function validate(ReleaseGroupDtoCollection $releaseReleaseGroup): void;
}
