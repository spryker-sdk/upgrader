<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\Adapter;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Infrastructure\Adapter\ReleaseAppClientAdapter;

class ReleaseAppClientAdapterTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetNewReleaseGroups(): void
    {
        // Arrange
        $releaseAppServiceMock = $this->createMock(ReleaseAppServiceInterface::class);
        $packageManagerAdapterMock = $this->createMock(PackageManagerAdapterInterface::class);
        $releaseAppResponseMock = $this->createMock(ReleaseAppResponse::class);
        // Asserts
        $releaseAppServiceMock->method('getNewReleaseGroups')
            ->with(new UpgradeInstructionsRequest([
                'spryker-shop/test' => '^1.0.0',
                'spryker/test' => '^1.0.0',
            ]))
        ->willReturn($releaseAppResponseMock);
        $packageManagerAdapterMock->method('getComposerLockFile')
            ->willReturn([
                'packages' => [
                    ['name' => 'spryker-shop/test', 'version' => '^1.0.0'],
                    ['name' => 'spryker/test', 'version' => '^1.0.0'],
                    ['name' => 'spryker-feature/test', 'version' => '^1.0.0'],
                ],
            ]);

        //Act
        $releaseAppResponse = (new ReleaseAppClientAdapter($releaseAppServiceMock, $packageManagerAdapterMock))->getNewReleaseGroups();

        // Asserts
        $this->assertSame($releaseAppResponseMock, $releaseAppResponse);
    }

    /**
     * @return void
     */
    public function testGetNewReleaseGroupsWithWrongStructure(): void
    {
        // Arrange
        $releaseAppServiceMock = $this->createMock(ReleaseAppServiceInterface::class);
        $packageManagerAdapterMock = $this->createMock(PackageManagerAdapterInterface::class);
        $releaseAppResponseMock = $this->createMock(ReleaseAppResponse::class);
        // Asserts
        $releaseAppServiceMock->method('getNewReleaseGroups')
            ->with(new UpgradeInstructionsRequest([]))
            ->willReturn($releaseAppResponseMock);
        $packageManagerAdapterMock->method('getComposerLockFile')
            ->willReturn([
                'package-list' => [
                    ['name' => 'spryker-shop/test', 'version' => '^1.0.0'],
                    ['name' => 'spryker/test', 'version' => '^1.0.0'],
                    ['name' => 'spryker-feature/test', 'version' => '^1.0.0'],
                ],
            ]);

        //Act
        $releaseAppResponse = (new ReleaseAppClientAdapter($releaseAppServiceMock, $packageManagerAdapterMock))->getNewReleaseGroups();

        // Asserts
        $this->assertSame($releaseAppResponseMock, $releaseAppResponse);
    }
}
