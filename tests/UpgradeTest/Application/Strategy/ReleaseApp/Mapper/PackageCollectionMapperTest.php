<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeTest\Application\Strategy\ReleaseApp\Mapper;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Application\Configuration\ReleaseAppConstant;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapper;
use Upgrade\Application\Strategy\ReleaseApp\Validator\PackageSoftValidator;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;
use Upgrade\Infrastructure\PackageManager\ComposerAdapter;

class PackageCollectionMapperTest extends TestCase
{
    /**
     * @return void
     */
    public function testMapModuleCollectionToPackageCollection(): void
    {
        $mapper = new PackageCollectionMapper(new PackageSoftValidator([]), $this->createMock(ComposerAdapter::class));

        $dtoCollection = new ModuleDtoCollection([
            new ModuleDto('symfony/finder', '5.3.0', ReleaseAppConstant::MODULE_TYPE_MINOR),
        ]);

        $packageCollection = $mapper->mapModuleCollectionToPackageCollection($dtoCollection);

        $this->assertEquals(
            new PackageCollection([new Package('symfony/finder', '5.3.0', '', '')]),
            $packageCollection,
        );
    }
}
