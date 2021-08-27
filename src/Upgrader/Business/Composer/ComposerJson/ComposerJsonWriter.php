<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer\ComposerJson;

use Upgrader\Business\Composer\Helper\JsonFile\JsonFileWriteHelper;

class ComposerJsonWriter implements ComposerJsonWriterInterface
{
    protected const FILENAME_JSON = 'composer.json';

    /**
     * @var \Upgrader\Business\Composer\Helper\JsonFile\JsonFileWriteHelper
     */
    protected $JsonFileWriteHelper;

    /**
     * @param JsonFileWriteHelper $JsonFileWriteHelper
     */
    public function __construct(JsonFileWriteHelper $JsonFileWriteHelper)
    {
        $this->JsonFileWriteHelper = $JsonFileWriteHelper;
    }

    /**
     * @param array $composerJsonArray
     *
     * @return bool
     */
    public function write(array $composerJsonArray): bool
    {
        return $this->JsonFileWriteHelper->writeToPath(self::FILENAME_JSON, $composerJsonArray);
    }
}
