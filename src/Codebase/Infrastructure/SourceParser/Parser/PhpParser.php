<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\Parser;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface;
use Codebase\Infrastructure\SourceFinder\SourceFinder;
use Error;
use Exception;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class PhpParser implements ParserInterface
{
    /**
     * @var string
     */
    protected const PARSER_EXTENSION = 'php';

    /**
     * @var \Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface
     */
    protected $parser;

    /**
     * @var \Codebase\Infrastructure\SourceFinder\SourceFinder
     */
    protected $sourceFinder;

    /**
     * @param \Codebase\Infrastructure\Dependency\Parser\CodebaseToParserInterface $parser
     * @param \Codebase\Infrastructure\SourceFinder\SourceFinder $sourceFinder
     */
    public function __construct(
        CodebaseToParserInterface $parser,
        SourceFinder $sourceFinder
    ) {
        $this->parser = $parser;
        $this->sourceFinder = $sourceFinder;
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

        $sources = [];
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            if ($file->getExtension() !== static::PARSER_EXTENSION) {
                continue;
            }

            $originalSyntaxTree = $this->parser->parse($file->getContents());

            if ($originalSyntaxTree) {
                $syntaxTree = $this->traverseOriginalSyntaxTree($originalSyntaxTree);
                $classNode = $this->sourceFinder->findClassNode($syntaxTree);
                if (!$classNode || !$classNode->namespacedName) {
                    continue;
                }

                $classString = (string)$classNode->namespacedName;
                $classCodebaseDto = $this->parseClass($classString, $codebaseSourceDto->getCoreNamespaces());
                if ($classCodebaseDto === null) {
                    continue;
                }

                $sources[$classString] = $classCodebaseDto;
            }
        }

        return $codebaseSourceDto->setPhpCodebaseSources($sources, $codebaseSourceDto->getType());
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
     * @param string $namespace
     * @param array<string> $coreNamespaces
     * @param \Codebase\Application\Dto\ClassCodebaseDto|null $transfer
     *
     * @return \Codebase\Application\Dto\ClassCodebaseDto|null
     */
    protected function parseClass(string $namespace, array $coreNamespaces = [], ?ClassCodebaseDto $transfer = null): ?ClassCodebaseDto
    {
        try {
            if (!class_exists($namespace) && !interface_exists($namespace)) {
                return null;
            }
            $projectClass = new ReflectionClass($namespace);
        } catch (Exception $logicException) {
            return null;
        } catch (Error $error) {
            return null;
        }

        if ($transfer === null) {
            $transfer = new ClassCodebaseDto($coreNamespaces);
        }
        $transfer->setClassName($namespace);
        $transfer->setConstants($projectClass->getConstants());
        $transfer->setMethods($projectClass->getMethods());
        $transfer->setInterfaces($projectClass->getInterfaces());
        $transfer->setTraits($projectClass->getTraits());
        $transfer->setReflection($projectClass);

        if ($coreNamespaces !== []) {
            $projectMethods = $this->getProjectMethods($projectClass->getName(), $projectClass->getMethods(), $coreNamespaces);
            $transfer->setProjectMethods($projectMethods);
        }

        $parentClass = $projectClass->getParentClass();

        if ($parentClass) {
            if ($coreNamespaces !== []) {
                $transfer->setCoreMethods($this->getCoreMethods($parentClass->getMethods(), $coreNamespaces));
            }

            $transfer->setParent(
                $this->parseClass($parentClass->getName(), $coreNamespaces),
            );
        }

        return $transfer;
    }

    /**
     * @param string $projectClassName
     * @param array<\ReflectionMethod> $methods
     * @param array<string> $coreNamespaces
     *
     * @return array<\ReflectionMethod>
     */
    protected function getProjectMethods(string $projectClassName, array $methods, array $coreNamespaces): array
    {
        return array_filter($methods, function ($method) use ($projectClassName, $coreNamespaces) {
            foreach ($coreNamespaces as $coreNamespace) {
                $isProjectClassMethod = $method->getDeclaringClass()->getName() == $projectClassName;
                $hasNoCoreNamespace = strpos($method->getDeclaringClass()->getNamespaceName(), $coreNamespace) !== 0;
                if ($isProjectClassMethod && $hasNoCoreNamespace) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @param array<\ReflectionMethod> $methods
     * @param array<string> $coreNamespaces
     *
     * @return array<\ReflectionMethod>
     */
    protected function getCoreMethods(array $methods, array $coreNamespaces): array
    {
        return array_filter($methods, function ($method) use ($coreNamespaces) {
            foreach ($coreNamespaces as $coreNamespace) {
                if (strpos($method->getDeclaringClass()->getNamespaceName(), $coreNamespace) === 0) {
                    return true;
                }
            }

            return false;
        });
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
