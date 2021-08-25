<?php

namespace Upgrader\Business\Composer\ComposerJson;

use Upgrader\Business\Composer\Helper\JsonFile\JsonFileWriteHelper;

class ComposerJsonWriter implements ComposerJsonWriterInterface
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
     * @param array $composerJsonArray
     *
     * @return bool
     */
    public function write(array $composerJsonArray): bool
    {
        return JsonFileWriteHelper::writeToPath($this->composerJsonFilePath, $composerJsonArray);
    }
}
