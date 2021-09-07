<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Json\Writer;

use Ergebnis\Json\Printer\PrinterInterface;
use Upgrader\Business\Exception\UpgraderException;

class ComposerJsonWriter implements ComposerJsonWriterInterface
{
    protected const FILENAME_JSON = 'composer.json';
    protected const INDENTATION_DEFAULT = 4;

    /**
     * @var \Ergebnis\Json\Printer\PrinterInterface
     */
    protected $printer;

    /**
     * ToDo Move to Bridge
     * @param \Ergebnis\Json\Printer\PrinterInterface $printer
     */
    public function __construct(PrinterInterface $printer)
    {
        $this->printer = $printer;
    }

    /**
     * @param array $composerJsonArray
     *
     * @return bool
     */
    public function write(array $composerJsonArray): bool
    {
        return $this->writeToPath($this->getFileName(), $composerJsonArray);
    }

    /**
     * @param string $path
     * @param array $body
     *
     * @return bool
     */
    protected function writeToPath(string $path, array $body): bool
    {
        $indentation = $this->detectIndentation($path);
        // ToDo Move to helper
        $encodedJson = (string)json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $encodedJson = $this->adjustIndentation($encodedJson, $indentation) . PHP_EOL;

        return (bool)file_put_contents($path, $encodedJson);
    }

    /**
     * @param string $filePath
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return int
     */
    protected function detectIndentation(string $filePath): int
    {
        if (!file_exists($filePath)) {
            throw new UpgraderException('File is not exist: ' . $filePath);
        }

        $content = (string)file_get_contents($filePath);

        preg_match('/^(.+)(".+":)/m', $content, $matches);

        if (!isset($matches[1])) {
            return static::INDENTATION_DEFAULT;
        }

        return strlen($matches[1]);
    }

    /**
     * @param string $encodedJson
     * @param int $indentation
     *
     * @return string
     */
    protected function adjustIndentation(string $encodedJson, int $indentation): string
    {
        return $this->printer->print($encodedJson, str_repeat(' ', $indentation));
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return static::FILENAME_JSON;
    }
}
