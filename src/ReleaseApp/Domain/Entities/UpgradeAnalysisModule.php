<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities;

use ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection;
use Upgrade\Application\Exception\UpgraderException;

class UpgradeAnalysisModule
{
    /**
     * @var string
     */
    protected const PACKAGE_KEY = 'package';

    /**
     * @var string
     */
    protected const MODULE_VERSIONS_KEY = 'module_versions';

    /**
     * @var array
     */
    protected array $body;

    /**
     * @var \ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection|null
     */
    protected ?UpgradeAnalysisModuleVersionCollection $moduleVersionCollection;

    /**
     * @param array $bodyArray
     */
    public function __construct(array $bodyArray)
    {
        $this->body = $bodyArray;
    }

    /**
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection
     */
    public function getModuleVersionCollection(): UpgradeAnalysisModuleVersionCollection
    {
        if ($this->moduleVersionCollection) {
            return $this->moduleVersionCollection;
        }

        $moduleVersionList = [];
        foreach ($this->body[static::MODULE_VERSIONS_KEY] as $moduleVersionData) {
            $moduleVersionList[] = new UpgradeAnalysisModuleVersion($moduleVersionData);
        }
        $this->moduleVersionCollection = new UpgradeAnalysisModuleVersionCollection($moduleVersionList);

        return $this->moduleVersionCollection;
    }

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return string
     */
    public function getPackage(): string
    {
        if (!array_key_exists(static::PACKAGE_KEY, $this->body)) {
            throw new UpgraderException('Key ' . static::PACKAGE_KEY . ' not found');
        }

        return $this->body[static::PACKAGE_KEY];
    }
}
