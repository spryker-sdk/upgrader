<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceFinder;

use Codebase\Infrastructure\Dependency\Finder\CodebaseToFinderInterface;
use Symfony\Component\Finder\Finder;

class SourceFinder
{
    /**
     * @var array
     */
    protected static $codebaseCache = [];

    /**
     * @var \Codebase\Infrastructure\Dependency\Finder\CodebaseToFinderInterface
     */
    protected $finder;

    /**
     * @var \Codebase\Infrastructure\SourceFinder\ClassNodeFinder
     */
    protected $classNodeFinder;

    /**
     * @param \Codebase\Infrastructure\Dependency\Finder\CodebaseToFinderInterface $finder
     * @param \Codebase\Infrastructure\SourceFinder\ClassNodeFinder $classNodeFinder
     */
    public function __construct(
        CodebaseToFinderInterface $finder,
        ClassNodeFinder $classNodeFinder
    ) {
        $this->finder = $finder;
        $this->classNodeFinder = $classNodeFinder;
    }

    /**
     * @param array<string> $extensions
     * @param array<string> $paths
     * @param array<string> $exclude
     *
     * @return \Symfony\Component\Finder\Finder
     */
    public function findSourceByExtension(array $extensions = [], array $paths = [], array $exclude = []): Finder
    {
        $hash = md5(implode('', $extensions) . implode('', $paths));
        if (isset(static::$codebaseCache[$hash])) {
            return static::$codebaseCache[$hash];
        }

        $finder = $this->finder->findSourceByExtension($extensions, $paths, $exclude);
        static::$codebaseCache[$hash] = $finder;

        return static::$codebaseCache[$hash];
    }

    /**
     * @param array $nodes
     *
     * @return mixed
     */
    public function findClassNode(array $nodes)
    {
        return $this->classNodeFinder->findClassNode($nodes);
    }
}
