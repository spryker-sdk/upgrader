<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\SourceParser\FileParser;

use Codebase\Application\Dto\XmlDto;
use Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface;
use Codebase\Infrastructure\SourceFinder\SourceFinder;
use SimpleXMLElement;

abstract class XmlFileParser implements FileParserInterface
{
    /**
     * @var string
     */
    protected const KEY_NAME = 'name';

    /**
     * @var string
     */
    protected const XML_EXTENSION = 'xml';

    /**
     * @var \Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface
     */
    protected $parser;

    /**
     * @var \Codebase\Infrastructure\SourceFinder\SourceFinder
     */
    protected $sourceFinder;

    public function __construct(
        CodebaseToParserInterface $parser,
        SourceFinder $sourceFinder
    ) {
        $this->parser = $parser;
        $this->sourceFinder = $sourceFinder;
    }

    /**
     * @param array<\SimpleXMLElement> $xmlElements
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    protected function getSources(array $xmlElements, string $childElement, string $fileRealPath): array
    {
        $sources = [];

        foreach ($xmlElements as $xmlSchema) {
            if ($xmlSchema->$childElement) {
                $xmlDto = new XmlDto(
                    $fileRealPath,
                    (string)$xmlSchema[static::KEY_NAME],
                );
                foreach ($xmlSchema->$childElement as $child) {
                    $xmlDto->addChildElement((string)$child[static::KEY_NAME]);
                }
                $sources[] = $xmlDto;
            }
        }

        return $sources;
    }

    protected function hasNamespaceInXml(SimpleXMLElement $simpleXmlElement, string $namespace): bool
    {
        return in_array($namespace, $simpleXmlElement->getNamespaces());
    }
}
