<?php

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;

interface ThresholdSoftValidatorInterface
{
    /**
     * @param ReleaseGroupDtoCollection $moduleDtoCollection
     * @return PackageManagerResponseDto
     */
    public function isWithInThreshold(ReleaseGroupDtoCollection $moduleDtoCollection): PackageManagerResponseDto;
}
