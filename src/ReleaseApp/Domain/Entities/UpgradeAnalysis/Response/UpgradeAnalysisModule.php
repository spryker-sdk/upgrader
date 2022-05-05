<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities\UpgradeAnalysis\Response;

use ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection;

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
    protected $bodyArray;

    /**
     * @var \ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection|null
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
     * @return \ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection
     */
    public function getModuleVersionCollection(): UpgradeAnalysisModuleVersionCollection
    {
        if ($this->moduleVersionCollection) {
            return $this->moduleVersionCollection;
        }

        $moduleVersionList = [];
        foreach ($this->bodyArray[static::MODULE_VERSIONS_KEY] as $moduleVersionData) {
            $moduleVersionList[] = new UpgradeAnalysisModuleVersion($moduleVersionData);
        }
        $this->moduleVersionCollection = new UpgradeAnalysisModuleVersionCollection($moduleVersionList);

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
