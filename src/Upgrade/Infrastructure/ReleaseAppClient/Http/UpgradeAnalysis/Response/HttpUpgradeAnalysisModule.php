<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response;

use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleVersionCollection;

class HttpUpgradeAnalysisModule
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
    protected $bodyArray;

    /**
     * @var \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleVersionCollection|null
     */
    protected $moduleVersionCollection;

    /**
     * @param array $bodyArray
     */
    public function __construct(array $bodyArray)
    {
        $this->bodyArray = $bodyArray;
    }

    /**
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleVersionCollection
     */
    public function getModuleVersionCollection(): HttpUpgradeAnalysisModuleVersionCollection
    {
        if ($this->moduleVersionCollection) {
            return $this->moduleVersionCollection;
        }

        $moduleVersionList = [];
        foreach ($this->bodyArray[static::MODULE_VERSIONS_KEY] as $moduleVersionData) {
            $moduleVersionList[] = new HttpUpgradeAnalysisModuleVersion($moduleVersionData);
        }
        $this->moduleVersionCollection = new HttpUpgradeAnalysisModuleVersionCollection($moduleVersionList);

        return $this->moduleVersionCollection;
    }

    /**
     * @return string
     */
    public function getPackage(): string
    {
        return $this->bodyArray[static::PACKAGE_KEY];
    }
}
