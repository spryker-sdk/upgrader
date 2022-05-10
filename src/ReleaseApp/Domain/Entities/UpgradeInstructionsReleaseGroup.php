<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities;

use DateTime;
use DateTimeInterface;
use ReleaseApp\Application\Configuration\ReleaseAppConst;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection;
use Upgrade\Application\Exception\UpgraderException;

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
     * @var string
     */
    protected const ID_KEY = 'id';

    /**
     * @var array
     */
    protected $bodyArray;

    /**
     * @var \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection|null
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
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->bodyArray[static::ID_KEY];
    }

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
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
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
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
