<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\SourceFinder\SourceFinder;

class SourceParser
{
    /**
     * @var string
     */
    protected const FINDER_PREFIX = '*.';

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
     * @param \Codebase\Infrastructure\SourceFinder\SourceFinder $sourceFinder
     * @param array<\Codebase\Infrastructure\SourceParser\Parser\ParserInterface> $sourceParsers
     */
    public function __construct(
        SourceFinder $sourceFinder,
        array $sourceParsers
    ) {
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
            ->setProjectPrefixes($codebaseRequestDto->getProjectPrefixes());

        foreach ($codebaseRequestDto->getPaths() as $type => $paths) {
            if ($paths === []) {
                continue;
            }
            $codebaseSourceDto = $codebaseSourceDto->setType($type);
            foreach ($this->sourceParsers as $sourceParser) {
                $extensions = [static::FINDER_PREFIX . $sourceParser->getExtension()];
                $finder = $this->sourceFinder->findSourceByExtension($extensions, $paths, $codebaseRequestDto->getExcludeList());
                $codebaseSourceDto = $sourceParser->parse($finder, $codebaseSourceDto);
            }
        }

        return $codebaseSourceDto;
    }
}
