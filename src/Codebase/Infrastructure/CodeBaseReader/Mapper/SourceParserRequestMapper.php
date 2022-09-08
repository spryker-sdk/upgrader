<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\CodeBaseReader\Mapper;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Application\Dto\SourceParserRequestDto;
use Codebase\Infrastructure\CodeBaseReader\SourceParserRequestMapperInterface;

class SourceParserRequestMapper implements SourceParserRequestMapperInterface
{
    /**
     * @param \Codebase\Application\Dto\CodeBaseRequestDto $codebaseRequestDto
     * @param \Codebase\Application\Dto\ConfigurationResponseDto $configurationResponseDto
     * @param array<\Codebase\Application\Dto\ModuleDto> $modules
     *
     * @return \Codebase\Application\Dto\SourceParserRequestDto
     */
    public function mapToSourceParserRequest(
        CodeBaseRequestDto $codebaseRequestDto,
        ConfigurationResponseDto $configurationResponseDto,
        array $modules
    ): SourceParserRequestDto {
        $projectPaths = $this->getProjectPaths(
            $codebaseRequestDto->getSrcPath(),
            $configurationResponseDto->getProjectPrefixes(),
            $modules,
        );

        return new SourceParserRequestDto(
            $projectPaths,
            $codebaseRequestDto->getCorePaths(),
            $codebaseRequestDto->getCoreNamespaces(),
            $configurationResponseDto->getProjectPrefixes(),
            $codebaseRequestDto->getExcludeList(),
        );
    }

    /**
     * @param string $srcPath
     * @param array<string> $projectPrefixes
     * @param array<\Codebase\Application\Dto\ModuleDto> $modules
     *
     * @return array<string>
     */
    protected function getProjectPaths(string $srcPath, array $projectPrefixes, array $modules): array
    {
        if ($modules) {
            return $this->getProjectPathsByModules($srcPath, $modules);
        }

        return $this->getProjectPathsByPrefixes($srcPath, $projectPrefixes);
    }

    /**
     * @param string $srcPath
     * @param array<string> $projectPrefixes
     *
     * @return array<string>
     */
    protected function getProjectPathsByPrefixes(string $srcPath, array $projectPrefixes): array
    {
        $projectDirectories = [];

        foreach ($projectPrefixes as $prefix) {
            $path = $srcPath . $prefix . DIRECTORY_SEPARATOR;
            if (is_dir($path)) {
                $projectDirectories[] = $path;
            }
        }

        return $projectDirectories;
    }

    /**
     * @param string $srcPath
     * @param array<\Codebase\Application\Dto\ModuleDto> $modules
     *
     * @return array<string>
     */
    protected function getProjectPathsByModules(string $srcPath, array $modules): array
    {
        $projectDirectories = [];

        foreach ($modules as $module) {
            $path = sprintf('%s%s/*/%s', $srcPath, $module->getNamespace(), $module->getName());
            $projectDirectories[] = $path;
        }

        return $projectDirectories;
    }
}
