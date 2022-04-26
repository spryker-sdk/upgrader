<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeInstructions\Response;

use DateTime;
use DateTimeInterface;
use ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection\HttpUpgradeInstructionModuleCollection;
use ReleaseAppClient\Domain\ReleaseAppConst;
use Upgrade\Infrastructure\Exception\UpgraderException;

class HttpUpgradeInstructionsReleaseGroup
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
     * @var \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection\HttpUpgradeInstructionModuleCollection|null
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
     * @return \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection\HttpUpgradeInstructionModuleCollection
     */
    public function getModuleCollection(): HttpUpgradeInstructionModuleCollection
    {
        if ($this->moduleCollection) {
            return $this->moduleCollection;
        }

        $moduleList = [];
        foreach ($this->bodyArray[static::MODULES_KEY] as $name => $moduleData) {
            $moduleList[] = new HttpUpgradeInstructionModule($moduleData, $name);
        }
        $this->moduleCollection = new HttpUpgradeInstructionModuleCollection($moduleList);

        return $this->moduleCollection;
    }
}
