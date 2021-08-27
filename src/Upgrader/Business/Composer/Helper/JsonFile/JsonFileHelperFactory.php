<?php

namespace Upgrader\Business\Composer\Helper\JsonFile;

use Ergebnis\Json\Printer\Printer;

class JsonFileHelperFactory
{
    /**
     * @var \Upgrader\Business\Composer\Helper\JsonFile\JsonFileReadHelper
     */
    protected $jsonFileReadHelper;

    /**
     * @var \Upgrader\Business\Composer\Helper\JsonFile\JsonFileWriteHelper
     */
    protected $jsonFileWriteHelper;

    /**
     * @return \Upgrader\Business\Composer\Helper\JsonFile\JsonFileReadHelper
     */
    public function createJsonFileReader(): JsonFileReadHelper
    {
        if ($this->jsonFileReadHelper === null) {
            $this->jsonFileReadHelper = new JsonFileReadHelper();
        }

        return $this->jsonFileReadHelper;
    }

    /**
     * @return \Upgrader\Business\Composer\Helper\JsonFile\JsonFileWriteHelper
     */
    public function createJsonFileWriter(): JsonFileWriteHelper
    {
        if ($this->jsonFileWriteHelper === null) {
            $this->jsonFileWriteHelper = new JsonFileWriteHelper($this->createJsonPrinter());
        }

        return $this->jsonFileWriteHelper;
    }

    /**
     * @return \Ergebnis\Json\Printer\Printer
     */
    protected function createJsonPrinter(): Printer
    {
        return new Printer();
    }
}
