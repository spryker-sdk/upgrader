<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

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
    protected const BACKPORTS_KEY = 'backports';

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
    protected const RATING_KEY = 'rating';

    /**
     * @var string
     */
    protected const META_KEY = 'meta';

    /**
     * @var string
     */
    protected const JIRA_KEY = 'jira';

    /**
     * @var string
     */
    protected const ISSUE_KEY = 'issue';

    /**
     * @var string
     */
    protected const ISSUE_LINK_KEY = 'issue_link';

    /**
     * @var string
     */
    protected const ID_KEY = 'id';

    /**
     * @var string
     */
    protected const SECURITY_KEY = 'is_security';

    /**
     * @var string
     */
    protected const INTEGRATION_GUIDE = 'integration_guide';

    /**
     * @var string
     */
    protected const MANUAL_ACTION_NEEDED = 'manual_action_needed';

    /**
     * @var array<mixed>
     */
    protected array $body;

    /**
     * @var \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection|null
     */
    protected ?UpgradeInstructionModuleCollection $moduleCollection = null;

    /**
     * @var \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection|null
     */
    protected ?UpgradeInstructionModuleCollection $backportModuleCollection = null;

    /**
     * @var \ReleaseApp\Domain\Entities\UpgradeInstructionMeta|null
     */
    protected ?UpgradeInstructionMeta $meta = null;

    /**
     * @param array<mixed> $bodyArray
     */
    public function __construct(array $bodyArray)
    {
        $this->body = $bodyArray;
        if (isset($this->body[static::META_KEY])) {
            $this->meta = new UpgradeInstructionMeta($this->body[static::META_KEY]);
        }
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
     * @return int
     */
    public function getRating(): int
    {
        return (int)$this->body[static::RATING_KEY];
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

    /**
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    public function getBackportModuleCollection(): UpgradeInstructionModuleCollection
    {
        if ($this->backportModuleCollection) {
            return $this->backportModuleCollection;
        }

        $moduleList = [];
        if (!isset($this->body[static::BACKPORTS_KEY])) {
            return new UpgradeInstructionModuleCollection();
        }
        foreach ($this->body[static::BACKPORTS_KEY] as $name => $moduleData) {
            $moduleList[] = new UpgradeInstructionModule($moduleData, $name);
        }
        $this->backportModuleCollection = new UpgradeInstructionModuleCollection($moduleList);

        return $this->backportModuleCollection;
    }

    /**
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructionMeta|null
     */
    public function getMeta(): ?UpgradeInstructionMeta
    {
        return $this->meta;
    }

    /**
     * @return string|null
     */
    public function getJiraIssue(): ?string
    {
        return isset($this->body[static::JIRA_KEY]) ? $this->body[static::JIRA_KEY][static::ISSUE_KEY] : null;
    }

    /**
     * @return string|null
     */
    public function getJiraIssueLink(): ?string
    {
        return isset($this->body[static::JIRA_KEY]) ? $this->body[static::JIRA_KEY][static::ISSUE_LINK_KEY] : null;
    }

    /**
     * @return bool
     */
    public function isSecurity(): bool
    {
        return (bool)($this->body[static::SECURITY_KEY] ?? false);
    }

    /**
     * @return string|null
     */
    public function getIntegrationGuide(): ?string
    {
        return $this->body[static::INTEGRATION_GUIDE] ?? null;
    }

    /**
     * @return bool
     */
    public function getManualActionNeeded(): bool
    {
        return $this->body[static::MANUAL_ACTION_NEEDED] ?? false;
    }
}
