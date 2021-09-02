<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\ComposerClient\ComposerFile;

use Upgrader\Business\Exception\UpgraderException;

abstract class AbstractJsonReader implements JsonReaderInterface
{
    /**
     * @return string
     */
    abstract public function getFileName(): string;

    /**
     * @return array
     */
    public function read(): array
    {
        return $this->readFromPath($this->getFileName());
    }

    /**
     * @param string $path
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return array
     */
    protected function readFromPath(string $path): array
    {
        if (!file_exists($path)) {
            throw new UpgraderException('File is not exist: ' . $path);
        }

        $fileContent = (string)file_get_contents($path);

        return json_decode($fileContent, true);
    }
}
