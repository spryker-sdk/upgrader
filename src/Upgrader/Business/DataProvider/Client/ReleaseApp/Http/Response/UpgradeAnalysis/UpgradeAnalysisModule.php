<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis;

use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleVersionCollection;

class UpgradeAnalysisModule
{
    public const PACKAGE_KEY = 'package';
    public const MODULE_VERSIONS_KEY = 'module_versions';

    /**
     * @var array
     */
    protected $bodyArray;

    /**
     * @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleVersionCollection
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
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleVersionCollection
     */
    public function getModuleVersionCollection(): UpgradeAnalysisModuleVersionCollection
    {
        if (!$this->moduleVersionCollection instanceof UpgradeAnalysisModuleVersionCollection) {
            $moduleVersionList = [];
            foreach ($this->bodyArray[self::MODULE_VERSIONS_KEY] as $moduleVersionData) {
                $moduleVersionList[] = new UpgradeAnalysisModuleVersion($moduleVersionData);
            }
            $this->moduleVersionCollection = new UpgradeAnalysisModuleVersionCollection($moduleVersionList);
        }

        return $this->moduleVersionCollection;
    }

    /**
     * @return string
     */
    public function getPackage(): string
    {
        return $this->bodyArray[self::PACKAGE_KEY];
    }
}
