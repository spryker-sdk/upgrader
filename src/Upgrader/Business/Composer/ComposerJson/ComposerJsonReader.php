<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer\ComposerJson;

use Exception;
use Upgrader\Business\Composer\Helper\JsonFile\JsonFileReadHelper;

class ComposerJsonReader implements ComposerJsonReaderInterface
{

    /**
     * @var string
     */
    private $composerJsonFilePath;

    /**
     * @param string $composerJsonFilePath
     */
    public function __construct(string $composerJsonFilePath)
    {
        $this->composerJsonFilePath = $composerJsonFilePath;
    }

    /**
     * @return array
     */
    public function read(): array
    {
        return JsonFileReadHelper::readFromPath($this->composerJsonFilePath);
    }
}
