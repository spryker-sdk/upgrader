<?php

namespace Upgrader\Business\Upgrader\Validator\ReleaseGroup\Command;

use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;
use Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface;

interface ReleaseGroupValidateCommandInterface extends CommandInterface
{
    /**
     * @param \Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface $releaseGroupValidator
     */
    public function __construct(ReleaseGroupValidatorInterface $releaseGroupValidator);

    /**
     * @param mixed $releaseGroup
     *
     * @return void
     */
    public function setReleaseGroup(ReleaseGroup $releaseGroup): void;
}
