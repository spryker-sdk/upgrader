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
     * @var string
     */
    protected const CORE_PATH_FORMAT = '%s/*';

    /**
     * @var string
     */
    protected const PROJECT_PATH_FORMAT = '%s/*/*';

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
            $directories = glob(sprintf(static::CORE_PATH_FORMAT, $corePath), GLOB_ONLYDIR);
            foreach ((array)$directories as $dir) {
                $coreModules[] = $this->snakeCaseToCamelCase(basename((string)$dir));
            }
        }
        $codebaseSourceDto->setCoreModuleNames(array_unique($coreModules, SORT_STRING));

        $projectModules = [];
        foreach ($codebaseRequestDto->getProjectPaths() as $projectPath) {
            $directories = glob(sprintf(static::PROJECT_PATH_FORMAT, $projectPath), GLOB_ONLYDIR);
            foreach ((array)$directories as $dir) {
                $projectModules[] = $this->removeRegionCode(basename((string)$dir));
            }
        }
        $codebaseSourceDto->setProjectModuleNames(array_unique($projectModules, SORT_STRING));

        return $codebaseSourceDto;
    }

    /**
     * @param string $input
     * @param string $separator
     *
     * @return string
     */
    protected function snakeCaseToCamelCase(string $input, string $separator = '-'): string
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    /**
     * @param string $input
     *
     * @return string
     */
    protected function removeRegionCode(string $input): string
    {
        $lastTwoCharacters = substr($input, -2);
        if (mb_strtoupper($lastTwoCharacters, 'utf-8') === $lastTwoCharacters) {
            return substr($input, 0, -2);
        }

        return $input;
    }
}
