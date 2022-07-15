<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\CodeBaseReader;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Application\Dto\SourceParserRequestDto;

class SourceParserRequestMapper implements SourceParserRequestMapperInterface
{
    /**
     * @param \Codebase\Application\Dto\CodeBaseRequestDto $codebaseRequestDto
     * @param \Codebase\Application\Dto\ConfigurationResponseDto $configurationResponseDto
     *
     * @return \Codebase\Application\Dto\SourceParserRequestDto
     */
    public function mapToSourceParserRequest(
        CodeBaseRequestDto $codebaseRequestDto,
        ConfigurationResponseDto $configurationResponseDto
    ): SourceParserRequestDto {
        $projectPaths = $this->getProjectPaths($codebaseRequestDto->getSrcPath(), $configurationResponseDto->getProjectPrefixes());

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
     *
     * @return array<string>
     */
    protected function getProjectPaths(string $srcPath, array $projectPrefixes): array
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
}
