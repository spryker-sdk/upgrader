<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
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
