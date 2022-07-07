<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser;

use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\SourceParserRequestDto;
use Codebase\Infrastructure\SourceFinder\SourceFinder;
use Codebase\Infrastructure\SourceParser\FileParser\PhpParser;

class SourceParser implements SourceParserInterface
{
    /**
     * @var string
     */
    protected const FINDER_PREFIX = '*.';

    /**
     * @var \Codebase\Infrastructure\SourceFinder\SourceFinder
     */
    protected $sourceFinder;

    /**
     * @var array<\Codebase\Infrastructure\SourceParser\FileParser\FileParserInterface>
     */
    protected array $fileParsers;

    /**
     * @var array<\Codebase\Infrastructure\SourceParser\StructureParser\StructureParserInterface>
     */
    protected array $structureParsers;

    /**
     * @param \Codebase\Infrastructure\SourceFinder\SourceFinder $sourceFinder
     * @param array<\Codebase\Infrastructure\SourceParser\FileParser\FileParserInterface> $sourceParsers
     * @param array<\Codebase\Infrastructure\SourceParser\StructureParser\StructureParserInterface> $structureParsers
     */
    public function __construct(
        SourceFinder $sourceFinder,
        array $sourceParsers,
        array $structureParsers
    ) {
        $this->sourceFinder = $sourceFinder;
        $this->fileParsers = $sourceParsers;
        $this->structureParsers = $structureParsers;
    }

    /**
     * @param \Codebase\Application\Dto\SourceParserRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parseSource(SourceParserRequestDto $codebaseRequestDto): CodebaseSourceDto
    {
        $codebaseSourceDto = new CodebaseSourceDto(
            $codebaseRequestDto->getCoreNamespaces(),
            $codebaseRequestDto->getProjectPrefixes(),
        );

        foreach ($this->structureParsers as $structureParser) {
            $codebaseSourceDto = $structureParser->parse($codebaseRequestDto, $codebaseSourceDto);
        }

        foreach ($codebaseRequestDto->getPaths() as $type => $paths) {
            if ($paths === []) {
                continue;
            }
            $codebaseSourceDto = $codebaseSourceDto->setType($type);
            foreach ($this->fileParsers as $fileParser) {
                if ($type === SourceParserRequestDto::CORE_TYPE && $fileParser->getExtension() === PhpParser::PARSER_EXTENSION) {
                    continue;
                }
                $extensions = [static::FINDER_PREFIX . $fileParser->getExtension()];
                $finder = $this->sourceFinder->findSourceByExtension($extensions, $paths, $codebaseRequestDto->getExcludeList());
                $codebaseSourceDto = $fileParser->parse($finder, $codebaseSourceDto);
            }
        }

        return $codebaseSourceDto;
    }
}
