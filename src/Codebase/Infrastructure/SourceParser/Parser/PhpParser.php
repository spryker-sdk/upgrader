<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\Parser;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\SourceParserRequestDto;
use Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface;
use Codebase\Infrastructure\SourceFinder\SourceFinder;
use Codebase\Infrastructure\SourceParser\Cache\SourceCache;
use Codebase\Infrastructure\SourceParser\Mapper\ReflectionClassToClassCodebaseDtoMapperInterface;
use Error;
use Exception;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 * @phpstan-template T of object
 */
class PhpParser implements ParserInterface
{
    /**
     * @var string
     */
    protected const PARSER_EXTENSION = 'php';

    /**
     * @var \Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface
     */
    protected $sourceParser;

    /**
     * @var \Codebase\Infrastructure\SourceFinder\SourceFinder
     */
    protected $sourceFinder;

    /**
     * @var \Codebase\Infrastructure\SourceParser\Mapper\ReflectionClassToClassCodebaseDtoMapperInterface
     */
    protected ReflectionClassToClassCodebaseDtoMapperInterface $sourceToDtoMapper;

    /**
     * @var \Codebase\Infrastructure\SourceParser\Cache\SourceCache
     */
    protected $sourceCache;

    /**
     * @param \Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface $sourceParser
     * @param \Codebase\Infrastructure\SourceFinder\SourceFinder $sourceFinder
     * @param \Codebase\Infrastructure\SourceParser\Mapper\ReflectionClassToClassCodebaseDtoMapperInterface $sourceToDtoMapper
     * @param \Codebase\Infrastructure\SourceParser\Cache\SourceCache $sourceCache
     */
    public function __construct(
        CodebaseToParserInterface $sourceParser,
        SourceFinder $sourceFinder,
        ReflectionClassToClassCodebaseDtoMapperInterface $sourceToDtoMapper,
        SourceCache $sourceCache
    ) {
        $this->sourceParser = $sourceParser;
        $this->sourceFinder = $sourceFinder;
        $this->sourceToDtoMapper = $sourceToDtoMapper;
        $this->sourceCache = $sourceCache;
    }

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
        $this->requireAutoload();
        $this->defineEnvironmentVariables();

        $isCoreType = $codebaseSourceDto->getType() == SourceParserRequestDto::CORE_TYPE;

        if ($isCoreType) {
            $cachedSources = $this->sourceCache->getSourceCacheType()->readCache(
                $this->sourceCache->getCacheIdentifier(),
                static::PARSER_EXTENSION
            );

            if ($cachedSources) {
                $codebaseSourceDto->setPhpCodebaseSources($cachedSources, $codebaseSourceDto->getType());
                return $codebaseSourceDto;
            }
        }

        $sources = $this->parsePhpCodebase($finder, $codebaseSourceDto);
        if ($isCoreType) {
            $cacheIdentifier = $this->sourceCache->getCacheIdentifier();
            $this->sourceCache->getSourceCacheType()->writeCache(
                $cacheIdentifier,
                static::PARSER_EXTENSION,
                $sources,
            );
        }

        return $codebaseSourceDto->setPhpCodebaseSources($sources, $codebaseSourceDto->getType());
    }

    /**
     * @param \Symfony\Component\Finder\Finder $finder
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    protected function parsePhpCodebase(Finder $finder, CodebaseSourceDto $codebaseSourceDto): array
    {
        $sources = [];
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            if ($file->getExtension() !== static::PARSER_EXTENSION) {
                continue;
            }

            $originalSyntaxTree = $this->sourceParser->parse($file->getContents());
            if ($originalSyntaxTree) {
                $syntaxTree = $this->traverseOriginalSyntaxTree($originalSyntaxTree);
                $classNode = $this->sourceFinder->findClassNode($syntaxTree);
                if (!$classNode || !$classNode->namespacedName) {
                    continue;
                }

                $classString = (string)$classNode->namespacedName;
                $classCodebaseDto = $this->parseClass(
                    $classString,
                    $codebaseSourceDto->getProjectPrefixes(),
                    $codebaseSourceDto->getCoreNamespaces(),
                );
                if ($classCodebaseDto === null) {
                    continue;
                }

                $sources[$classString] = $classCodebaseDto;
            }
        }

        return $sources;
    }

    /**
     * @return void
     */
    protected function requireAutoload(): void
    {
        $autoloadPaths = [
            getcwd() . '/vendor/autoload.php',
        ];

        foreach ($autoloadPaths as $autoloadPath) {
            if (file_exists($autoloadPath)) {
                require_once $autoloadPath;
            }
        }
    }

    /**
     * @return void
     */
    protected function defineEnvironmentVariables(): void
    {
        if (!defined('APPLICATION_SOURCE_DIR')) {
            define('APPLICATION_SOURCE_DIR', getcwd() . '/src');
        }

        if (!defined('APPLICATION_VENDOR_DIR')) {
            define('APPLICATION_VENDOR_DIR', getcwd() . '/vendor');
        }
    }

    /**
     * @phpstan-param \Codebase\Application\Dto\ClassCodebaseDto<T>|null $transfer
     *
     * @phpstan-return \Codebase\Application\Dto\ClassCodebaseDto<T>|null
     *
     * @param string $namespace
     * @param array<string> $projectPrefixes
     * @param array<string> $coreNamespaces
     * @param \Codebase\Application\Dto\ClassCodebaseDto|null $transfer
     *
     * @return \Codebase\Application\Dto\ClassCodebaseDto|null
     */
    protected function parseClass(
        string $namespace,
        array $projectPrefixes,
        array $coreNamespaces = []
    ): ?ClassCodebaseDto {
        try {
            if (!class_exists($namespace) && !interface_exists($namespace)) {
                return null;
            }
            /** @phpstan-var \ReflectionClass<T> $projectClass */
            $projectClass = new ReflectionClass($namespace);
        } catch (Exception $logicException) {
            return null;
        } catch (Error $error) {
            return null;
        }

        return $this->sourceToDtoMapper->map($projectClass, $projectPrefixes, $coreNamespaces);
    }

    /**
     * @param array<\PhpParser\Node\Stmt>|null $originalSyntaxTree
     *
     * @return array<\PhpParser\Node>
     */
    protected function traverseOriginalSyntaxTree(?array $originalSyntaxTree): array
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new CloningVisitor());
        $nodeTraverser->addVisitor(new NameResolver());

        return $nodeTraverser->traverse($originalSyntaxTree);
    }
}
