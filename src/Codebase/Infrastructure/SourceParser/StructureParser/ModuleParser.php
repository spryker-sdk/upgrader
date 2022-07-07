<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\StructureParser;

use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\SourceParserRequestDto;

class ModuleParser implements StructureParserInterface
{
    /**
     * @param \Codebase\Application\Dto\SourceParserRequestDto $codebaseRequestDto
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parse(SourceParserRequestDto $codebaseRequestDto, CodebaseSourceDto $codebaseSourceDto): CodebaseSourceDto
    {
        $coreModules = [];
        foreach ($codebaseRequestDto->getCorePaths() as $corePath) {
            foreach ((array)glob(sprintf('%s/*', $corePath), GLOB_ONLYDIR) as $dir) {
                $coreModules[] = basename((string)$dir);
            }
        }
        $codebaseSourceDto->setCoreModuleNames(array_unique($coreModules, SORT_STRING));

        $projectModules = [];
        foreach ($codebaseRequestDto->getProjectPaths() as $projectPath) {
            foreach ((array)glob(sprintf('%s/*/*', $projectPath), GLOB_ONLYDIR) as $dir) {
                $projectModules[] = basename((string)$dir);
            }
        }
        $codebaseSourceDto->setProjectModuleNames(array_unique($projectModules, SORT_STRING));

        return $codebaseSourceDto;
    }
}
