<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer\Helper\JsonFile;

use Ergebnis\Json\Printer\Printer;
use Exception;

class JsonFileWriteHelper
{
    protected const INDENTATION_DEFAULT = 4;

    /**
     * @var \Ergebnis\Json\Printer\Printer
     */
    protected $jsonPrinter;

    /**
     * @param \Ergebnis\Json\Printer\Printer $jsonPrinter
     */
    public function __construct(Printer $jsonPrinter)
    {
        $this->jsonPrinter = $jsonPrinter;
    }

    /**
     * @param string $path
     * @param array $body
     *
     * @return bool
     */
    public function writeToPath(string $path, array $body): bool
    {
        $indentation = $this->detectIndentation($path);
        $encodedJson = (string)json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $printed = $this->jsonPrinter->print($encodedJson, str_repeat(' ', $indentation));

        return (bool)file_put_contents($path, $printed);
    }

    /**
     * @param string $filePath
     *
     * @throws \Exception
     *
     * @return int
     */
    protected function detectIndentation(string $filePath): int
    {
        if (!file_exists($filePath)) {
            throw new Exception('File is not exist: ' . $filePath);
        }

        $content = (string)file_get_contents($filePath);
        preg_match('/^(.+)(".+":)/m', $content, $matches);
        if (!$matches[1]) {
            return static::INDENTATION_DEFAULT;
        }

        return strlen($matches[1]);
    }
}
