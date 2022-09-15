<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodebaseTest\Infrastructure\SourceParser\Parser;

use Codebase\Application\Dto\CodebaseSourceDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Finder\Finder;

class BaseParser extends KernelTestCase
{
    /**
     * @var array<string>
     */
    protected const PROJECT_PREFIX = ['Test'];

    /**
     * @var array<string>
     */
    protected const CORE_NAMESPACES = ['TestCore'];

    /**
     * @return array<string>
     */
    protected function getPaths(): array
    {
        return [
            'project' => [APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Project/'],
            'core' => [APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Core/'],
        ];
    }

    /**
     * @param array<string> $paths
     * @param array<string> $extensions
     * @param array<string> $exclude
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function getFinder(array $paths = [], array $extensions = [], array $exclude = []): Finder
    {
        $finder = Finder::create();

        return $finder->in($paths)->name($extensions)->exclude($exclude);
    }

    /**
     * @param string $parserClass
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     * @param string $extension
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    protected function runParser(string $parserClass, CodebaseSourceDto $codebaseSourceDto, string $extension): CodebaseSourceDto
    {
        foreach ($this->getPaths() as $type => $path) {
            $codebaseSourceDto->setType($type);
            $finder = $this->getFinder($path, ['*.' . $extension]);
            $codebaseSourceDto = static::bootKernel()->getContainer()->get($parserClass)->parse($finder, $codebaseSourceDto);
        }

        return $codebaseSourceDto;
    }

    /**
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    protected function createCodebaseSourceDto(): CodebaseSourceDto
    {
        return new CodebaseSourceDto(static::PROJECT_PREFIX, static::CORE_NAMESPACES);
    }
}
