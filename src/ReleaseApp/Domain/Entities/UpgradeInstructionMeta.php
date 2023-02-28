<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Domain\Entities;

use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection;
use SprykerSdk\Integrator\Common\UtilText\TextCaseHelper;

class UpgradeInstructionMeta
{
    /**
     * @var string
     */
    protected const INCLUDE_KEY = 'include';

    /**
     * @var string
     */
    protected const EXCLUDE_KEY = 'exclude';

    /**
     * @var string
     */
    protected const CONFLICT_KEY = 'conflict';

    /**
     * @var \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection|null
     */
    protected ?UpgradeInstructionModuleCollection $includeModuleCollection = null;

    /**
     * @var \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection|null
     */
    protected ?UpgradeInstructionModuleCollection $excludeModuleCollection = null;

    /**
     * @var \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection|null
     */
    protected ?UpgradeInstructionModuleCollection $conflictModuleCollection = null;

    /**
     * @var array<mixed>
     */
    protected array $body;

    /**
     * @param array<mixed> $body
     */
    public function __construct(array $body)
    {
        $this->body = $body;
    }

    /**
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    public function getInclude(): UpgradeInstructionModuleCollection
    {
        if ($this->includeModuleCollection) {
            return $this->includeModuleCollection;
        }

        $this->includeModuleCollection = new UpgradeInstructionModuleCollection(
            $this->getModuleListByKey(static::INCLUDE_KEY),
        );

        return $this->includeModuleCollection;
    }

    /**
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    public function getExclude(): UpgradeInstructionModuleCollection
    {
        if ($this->excludeModuleCollection) {
            return $this->excludeModuleCollection;
        }

        $this->excludeModuleCollection = new UpgradeInstructionModuleCollection(
            $this->getModuleListByKey(static::EXCLUDE_KEY),
        );

        return $this->excludeModuleCollection;
    }

    /**
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    public function getConflict(): UpgradeInstructionModuleCollection
    {
        if ($this->conflictModuleCollection) {
            return $this->conflictModuleCollection;
        }

        $this->conflictModuleCollection = new UpgradeInstructionModuleCollection(
            $this->getModuleListByKey(static::CONFLICT_KEY),
        );

        return $this->conflictModuleCollection;
    }

    /**
     * @param string $key
     *
     * @return array<\ReleaseApp\Domain\Entities\UpgradeInstructionModule>
     */
    protected function getModuleListByKey(string $key): array
    {
        $list = [];
        if (!array_key_exists($key, $this->body)) {
            return $list;
        }

        foreach ($this->body[$key] as $name => $version) {
            $list[] = new UpgradeInstructionModule(
                [UpgradeInstructionModule::VERSION_KEY => $version],
                $this->convertToPackageFormat($name),
            );
        }

        return $list;
    }

    /**
     * @param string $originName
     *
     * @return string
     *
     * Spryker.SymfonyMailer => spryker/symfony-mailer
     */
    protected function convertToPackageFormat(string $originName): string
    {
        [$organization, $package] = explode('.', $originName);

        return implode('/', [
            TextCaseHelper::camelCaseToDash($organization),
            TextCaseHelper::camelCaseToDash($package),
        ]);
    }
}
