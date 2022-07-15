<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\FileParser;

use Codebase\Application\Dto\CodebaseSourceDto;
use SimpleXMLElement;
use Symfony\Component\Finder\Finder;

class TransferSchemaParser extends XmlFileParser
{
    /**
     * @var string
     */
    protected const KEY_CHILD_ELEMENT = 'property';

    /**
     * @var string
     */
    protected const PARSER_EXTENSION = 'transfer.xml';

    /**
     * @var string
     */
    protected const TRANSFER_NAMESPACE = 'spryker:transfer-01';

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return static::PARSER_EXTENSION;
    }

    /**
     * @param \Symfony\Component\Finder\Finder $finder
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parse(Finder $finder, CodebaseSourceDto $codebaseSourceDto): CodebaseSourceDto
    {
        $sources = [];
        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            if ($file->getExtension() !== static::XML_EXTENSION) {
                continue;
            }

            $fileContent = (string)file_get_contents((string)$file->getRealPath());

            if (trim($fileContent) === '' || !$fileContent) {
                continue;
            }

            $simpleXmlElement = simplexml_load_string($fileContent);

            if (!$simpleXmlElement) {
                continue;
            }

            if (!in_array(static::TRANSFER_NAMESPACE, $simpleXmlElement->getNamespaces())) {
                continue;
            }

            $simpleXmlElement = $this->getSimpleXmlTransferElements($simpleXmlElement);

            if (!$simpleXmlElement) {
                continue;
            }

            $newSources = $this->getSources($simpleXmlElement, static::KEY_CHILD_ELEMENT, (string)$file->getRealPath());
            $sources = array_merge($newSources, $sources);
        }

        return $codebaseSourceDto->setTransferSchemaCodebaseSources($sources, $codebaseSourceDto->getType());
    }

    /**
     * @param \SimpleXMLElement $simpleXmlElement
     *
     * @return array<\SimpleXMLElement>
     */
    protected function getSimpleXmlTransferElements(SimpleXMLElement $simpleXmlElement): array
    {
        $namespace = 'spryker:transfer-01';
        if ($this->hasNamespaceInXml($simpleXmlElement, $namespace)) {
            $simpleXmlElement->registerXPathNamespace('t', $namespace);

            return $simpleXmlElement->xpath('//t:transfer') ?: [];
        }

        return $simpleXmlElement->xpath('//transfer') ?: [];
    }
}
