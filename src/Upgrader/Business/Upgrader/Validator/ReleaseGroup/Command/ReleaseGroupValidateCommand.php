<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator\ReleaseGroup\Command;

use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;
use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface;

class ReleaseGroupValidateCommand implements ReleaseGroupValidateCommandInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface
     */
    protected $releaseGroupValidator;

    /**
     * @var \Upgrader\Business\DataProvider\Entity\ReleaseGroup|null
     */
    protected $releaseGroup;

    /**
     * @param \Upgrader\Business\Upgrader\Validator\ReleaseGroup\ReleaseGroupValidatorInterface $releaseGroupValidator
     */
    public function __construct(ReleaseGroupValidatorInterface $releaseGroupValidator)
    {
        $this->releaseGroupValidator = $releaseGroupValidator;
    }

    /**
     * @param mixed $releaseGroup
     *
     * @return void
     */
    public function setReleaseGroup(ReleaseGroup $releaseGroup): void
    {
        $this->releaseGroup = $releaseGroup;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Release group validator';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Command validate release group';
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'validator:release-group';
    }

    /**
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function run(): CommandResponse
    {
        if (!$this->releaseGroup) {
            throw new UpgraderException('ReleaseGroupValidateCommand releaseGroup property is not define');
        }

        try {
            $this->releaseGroupValidator->validate($this->releaseGroup);
        } catch (UpgraderException $exception) {
            return new CommandResponse(false, $this->getName(), $exception->getMessage());
        }

        return new CommandResponse(true, $this->getName());
    }
}
