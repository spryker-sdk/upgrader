<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodebaseTest\Infrastructure\CodeBaseReader\Mapper;

use Codebase\Application\Dto\ModuleDto;
use Codebase\Infrastructure\CodeBaseReader\Mapper\ModuleOptionMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Upgrade\Application\Exception\UpgraderException;

class ModuleOptionMapperMapperTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testSuccessSampleModuleList(): void
    {
        //Arrange
        $mapper = new ModuleOptionMapper();

        //Act
        $moduleList = $mapper->mapToModuleList('Pyz.ModuleA Pyz.ModuleB');

        //Assert
        $this->assertEquals($moduleList, $this->getModuleList());
    }

    /**
     * @return void
     */
    public function testFailedIncorrectInput(): void
    {
        //Arrange
        $mapper = new ModuleOptionMapper();

        //Assert
        $this->expectException(UpgraderException::class);

        //Act
        $moduleList = $mapper->mapToModuleList('Pyz_ModuleA|Pyz_ModuleB');
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
