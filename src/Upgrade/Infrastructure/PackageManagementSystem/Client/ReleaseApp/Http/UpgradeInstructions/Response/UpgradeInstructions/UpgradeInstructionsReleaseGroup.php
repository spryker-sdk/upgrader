<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions;

use DateTime;
use DateTimeInterface;
use Upgrade\Infrastructure\Exception\UpgraderException;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\UpgradeInstructionModuleCollection;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\ReleaseAppConst;

class UpgradeInstructionsReleaseGroup
{
    /**
     * @var string
     */
    protected const MODULES_KEY = 'modules';

    /**
     * @var string
     */
    protected const RELEASED_KEY = 'released';

    /**
     * @var string
     */
    protected const PROJECT_CHANGES_KEY = 'project_changes';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var array
     */
    protected $bodyArray;

    /**
     * @var \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\UpgradeInstructionModuleCollection|null
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
        return $this->bodyArray[static::NAME_KEY];
    }

    /**
     * @return bool
     */
    public function isContainsProjectChanges(): bool
    {
        return $this->bodyArray[static::PROJECT_CHANGES_KEY];
    }

    /**
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return \DateTimeInterface
     */
    public function getReleased(): DateTimeInterface
    {
        if (!isset($this->bodyArray[static::RELEASED_KEY])) {
            $message = sprintf('%s %s', 'Undefined key:', static::RELEASED_KEY);

            throw new UpgraderException($message);
        }

        $dataTime = DateTime::createFromFormat(
            ReleaseAppConst::RESPONSE_DATA_TIME_FORMAT,
            $this->bodyArray[static::RELEASED_KEY],
        );

        if (!$dataTime) {
            $message = sprintf('%s %s', 'API invalid datatime format:', $this->bodyArray[static::RELEASED_KEY]);

            throw new UpgraderException($message);
        }

        return $dataTime;
    }

    /**
     * @return \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\UpgradeInstructionModuleCollection
     */
    public function getModuleCollection(): UpgradeInstructionModuleCollection
    {
        if ($this->moduleCollection) {
            return $this->moduleCollection;
        }

        $moduleList = [];
        foreach ($this->bodyArray[static::MODULES_KEY] as $name => $moduleData) {
            $moduleList[] = new UpgradeInstructionModule($moduleData, $name);
        }
        $this->moduleCollection = new UpgradeInstructionModuleCollection($moduleList);

        return $this->moduleCollection;
    }
}
