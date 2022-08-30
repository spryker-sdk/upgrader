<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodebaseTest\Infrastructure\CodeBaseReader;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Application\Dto\ModuleDto;
use Codebase\Infrastructure\CodeBaseReader\SourceParserRequestMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SourceParserRequestMapperTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testSuccessProjectPathMapByModules(): void
    {
        //Arrange
        $mapper = new SourceParserRequestMapper();

        $codeBaseRequestDto = new CodeBaseRequestDto(
            'tooling.yml',
            'src/',
            ['vendor/spryker'],
            ['Spryker'],
            ['SprykerTest'],
            $this->getModuleList(),
        );

        $configurationResponseDto = new ConfigurationResponseDto();

        //Act
        $sourceParserRequest = $mapper->mapToSourceParserRequest($codeBaseRequestDto, $configurationResponseDto);

        //Assert
        $this->assertEquals(
            [
                'project' => ['src/Pyz/*/ModuleA', 'src/Pyz/*/ModuleB'],
                'core' => ['vendor/spryker'],
            ],
            $sourceParserRequest->getPaths(),
        );
    }

    /**
     * @return void
     */
    public function testSuccessProjectPathMapByPrefixes(): void
    {
        //Arrange
        $mapper = new SourceParserRequestMapper();

        $codeBaseRequestDto = new CodeBaseRequestDto(
            'tooling.yml',
            getcwd() . '/tests/data/Evaluate/',
            ['vendor/spryker'],
            ['Spryker'],
            ['SprykerTest'],
            [],
        );

        $configurationResponseDto = new ConfigurationResponseDto([
            'upgrader' => [
                'prefixes' => [
                    'Project',
                ],
            ],
        ]);

        //Act
        $sourceParserRequest = $mapper->mapToSourceParserRequest($codeBaseRequestDto, $configurationResponseDto);

        //Assert
        $this->assertEquals(
            [
                'project' => [getcwd() . '/tests/data/Evaluate/Project/'],
                'core' => ['vendor/spryker'],
            ],
            $sourceParserRequest->getPaths(),
        );
    }

    /**
     * @return array<\Codebase\Application\Dto\ModuleDto>
     */
    protected function getModuleList(): array
    {
        return [
            (new ModuleDto('Pyz', 'ModuleA')),
            (new ModuleDto('Pyz', 'ModuleB')),
        ];
    }
}
