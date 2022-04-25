<?php

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Infrastructure\Exception\UpgraderException;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\Threshold\ThresholdValidatorInterface;

class ThresholdSoftValidator implements ThresholdSoftValidatorInterface
{
    /**
     * @var array<ThresholdValidatorInterface>
     */
    protected $validatorList = [];

    /**
     * @param array<ThresholdValidatorInterface> $validatorList
     */
    public function __construct(array $validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @param ReleaseGroupDtoCollection $groupDtoCollection
     * @return PackageManagerResponseDto
     */
    public function isWithInThreshold(ReleaseGroupDtoCollection $groupDtoCollection): PackageManagerResponseDto
    {
        try {
            foreach ($this->validatorList as $validator) {
                $validator->validate($groupDtoCollection);
            }
        } catch (UpgraderException $exception) {
            return new PackageManagerResponseDto(false, $exception->getMessage());
        }

        return new PackageManagerResponseDto(true);
    }
}
