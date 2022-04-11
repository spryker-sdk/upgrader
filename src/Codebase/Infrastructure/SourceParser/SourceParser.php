<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface;
use Codebase\Infrastructure\SourceFinder\SourceFinder;

class SourceParser
{
    /**
     * @var string
     */
    protected const SCHEMA_XML_EXTENSION = '*.schema.xml';

    /**
     * @var string
     */
    protected const PHP_EXTENSION = '*.php';

    /**
     * @var string
     */
    protected const TRANSFER_XML_EXTENSION = '*.transfer.xml';

    /**
     * @var array<string>
     */
    protected const EXTENSIONS = [
        self::SCHEMA_XML_EXTENSION,
        self::PHP_EXTENSION,
        self::TRANSFER_XML_EXTENSION,
    ];

    /**
     * @var \Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface
     */
    protected $parser;

    /**
     * @var \Codebase\Infrastructure\SourceFinder\SourceFinder
     */
    protected $sourceFinder;

    /**
     * @var array<\Codebase\Infrastructure\SourceParser\Parser\ParserInterface>
     */
    protected $sourceParsers;

    /**
     * @param \Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface $parser
     * @param \Codebase\Infrastructure\SourceFinder\SourceFinder $sourceFinder
     * @param array<\Codebase\Infrastructure\SourceParser\Parser\ParserInterface> $sourceParsers
     */
    public function __construct(
        CodebaseToParserInterface $parser,
        SourceFinder $sourceFinder,
        array $sourceParsers
    ) {
        $this->parser = $parser;
        $this->sourceFinder = $sourceFinder;
        $this->sourceParsers = $sourceParsers;
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parseSource(CodebaseRequestDto $codebaseRequestDto): CodebaseSourceDto
    {
        $codebaseSourceDto = (new CodebaseSourceDto())
            ->setCoreNamespaces($codebaseRequestDto->getCoreNamespaces())
            ->setProjectPrefixList($codebaseRequestDto->getProjectPrefixList());

        foreach ($codebaseRequestDto->getPaths() as $type => $paths) {
            if ($paths === []) {
                continue;
            }
            $codebaseSourceDto = $codebaseSourceDto->setType($type);
            foreach (static::EXTENSIONS as $extension) {
                $finder = $this->sourceFinder->findSourceByExtension([$extension], $paths, $codebaseRequestDto->getExcludeList());

                foreach ($this->sourceParsers as $sourceParser) {
                    if (strpos($extension, $sourceParser->getExtension())) {
                        $codebaseSourceDto = $sourceParser->parse($finder, $codebaseSourceDto);
                    }
                }
            }
        }

        return $codebaseSourceDto;
    }
}
