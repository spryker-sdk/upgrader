<?php

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator;

use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;
use Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection;

interface ThresholdSoftValidatorInterface
{
    /**
     * @param ReleaseGroupDtoCollection $moduleDtoCollection
     * @return PackageManagerResponseDto
     */
    public function isWithInThreshold(ReleaseGroupDtoCollection $moduleDtoCollection): PackageManagerResponseDto;
}
