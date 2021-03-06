<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities;

use DateTime;
use DateTimeInterface;
use ReleaseApp\Application\Configuration\ReleaseAppConstant;
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
     * @var array<mixed>
     */
    protected array $body;

    /**
     * @var \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection|null
     */
    protected ?UpgradeInstructionModuleCollection $moduleCollection = null;

    /**
     * @param array<mixed> $bodyArray
     */
    public function __construct(array $bodyArray)
    {
        $this->body = $bodyArray;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->body[static::NAME_KEY];
    }

    /**
     * @return bool
     */
    public function hasProjectChanges(): bool
    {
        return $this->body[static::PROJECT_CHANGES_KEY];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->body[static::ID_KEY];
    }

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return \DateTimeInterface
     */
    public function getReleased(): DateTimeInterface
    {
        if (!isset($this->body[static::RELEASED_KEY])) {
            $message = sprintf('%s %s', 'Undefined key:', static::RELEASED_KEY);

            throw new UpgraderException($message);
        }

        $dataTime = DateTime::createFromFormat(
            ReleaseAppConstant::RESPONSE_DATA_TIME_FORMAT,
            $this->body[static::RELEASED_KEY],
        );

        if (!$dataTime) {
            $message = sprintf('%s %s', 'API invalid datatime format:', $this->body[static::RELEASED_KEY]);

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
        foreach ($this->body[static::MODULES_KEY] as $name => $moduleData) {
            $moduleList[] = new UpgradeInstructionModule($moduleData, $name);
        }
        $this->moduleCollection = new UpgradeInstructionModuleCollection($moduleList);

        return $this->moduleCollection;
    }
}
