<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions;

use DateTime;
use DateTimeInterface;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\Collection\UpgradeInstructionModuleCollection;
use Upgrader\Business\DataProvider\Client\ReleaseApp\ReleaseAppConst;
use Upgrader\Business\Exception\UpgraderException;

class UpgradeInstructionsReleaseGroup
{
    public const MODULES_KEY = 'modules';
    public const RELEASED_KEY = 'released';
    public const PROJECT_CHANGES_KEY = 'project_changes';
    public const NAME_KEY = 'name';

    /**
     * @var array
     */
    protected $bodyArray;

    /**
     * @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\Collection\UpgradeInstructionModuleCollection
     */
    protected $moduleCollection;

    /**
     * @param array $bodyArray
     */
    public function __construct(array $bodyArray)
    {
        $this->bodyArray = $bodyArray;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->bodyArray[self::NAME_KEY];
    }

    /**
     * @return bool
     */
    public function isContainsProjectChanges(): bool
    {
        return $this->bodyArray[self::PROJECT_CHANGES_KEY];
    }

    /**
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return \DateTimeInterface
     */
    public function getReleased(): DateTimeInterface
    {
        $dataTime = DateTime::createFromFormat(
            ReleaseAppConst::RESPONSE_DATA_TIME_FORMAT,
            $this->bodyArray[self::RELEASED_KEY]
        );

        if (!$dataTime) {
            $message = sprintf('%s %s', 'API invalid datatime format:', $this->bodyArray[self::RELEASED_KEY]);

            throw new UpgraderException($message);
        }

        return $dataTime;
    }

    /**
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\Collection\UpgradeInstructionModuleCollection
     */
    public function getModuleCollection(): UpgradeInstructionModuleCollection
    {
        if (!$this->moduleCollection instanceof UpgradeInstructionModuleCollection) {
            $moduleList = [];
            foreach ($this->bodyArray[self::MODULES_KEY] as $name => $moduleData) {
                $moduleList[] = new UpgradeInstructionModule($moduleData, $name);
            }
            $this->moduleCollection = new UpgradeInstructionModuleCollection($moduleList);
        }

        return $this->moduleCollection;
    }
}
