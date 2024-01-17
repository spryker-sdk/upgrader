<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Expander;

use DateTime;
use PHPUnit\Framework\TestCase;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Expander\FeatureDevMasterPackageExpanderEventSubscriber;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;

class FeatureDevMasterPackageExpanderSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPreRequireShouldNotInvokeCheckerWhenFeatureDevMasterDisabled(): void
    {
        // Arrange & Assert
        $stepsResponseDtoMock = $this->createMock(StepsResponseDto::class);
        $stepsResponseDtoMock->expects($this->never())->method('getCurrentReleaseGroup');
        $event = new ReleaseGroupProcessorEvent($stepsResponseDtoMock);

        $featureDevMasterPackageExpanderEventSubscriber = new FeatureDevMasterPackageExpanderEventSubscriber(
            $this->createConfigurationProviderMock(true),
            $this->createMock(PackageManagerAdapterInterface::class),
            false,
        );

        // Act
        $featureDevMasterPackageExpanderEventSubscriber->onPreRequire($event);
    }

    /**
     * @return void
     */
    public function testOnPreRequireShouldNotInvokeCheckerWhenReleaseGroupIdEmpty(): void
    {
        // Arrange & Assert
        $stepsResponseDtoMock = $this->createMock(StepsResponseDto::class);
        $stepsResponseDtoMock->expects($this->never())->method('getCurrentReleaseGroup');
        $event = new ReleaseGroupProcessorEvent($stepsResponseDtoMock);

        $featureDevMasterPackageExpanderEventSubscriber = new FeatureDevMasterPackageExpanderEventSubscriber(
            $this->createConfigurationProviderMock(false),
            $this->createMock(PackageManagerAdapterInterface::class),
            true,
        );

        // Act
        $featureDevMasterPackageExpanderEventSubscriber->onPreRequire($event);
    }

    /**
     * @return void
     */
    public function testOnPreRequireShouldNotInvokeCheckerWhenCurrentReleaseGroupEmpty(): void
    {
        // Arrange & Assert
        $stepsResponseDtoMock = $this->createMock(StepsResponseDto::class);
        $stepsResponseDtoMock->method('getCurrentReleaseGroup')->willReturn(null);
        $event = new ReleaseGroupProcessorEvent($stepsResponseDtoMock);
        $packageManagerAdapterMock = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapterMock->expects($this->never())->method('getComposerJsonFile');

        $featureDevMasterPackageExpanderEventSubscriber = new FeatureDevMasterPackageExpanderEventSubscriber(
            $this->createConfigurationProviderMock(true),
            $packageManagerAdapterMock,
            true,
        );

        // Act
        $featureDevMasterPackageExpanderEventSubscriber->onPreRequire($event);
    }

    /**
     * @return void
     */
    public function testSetFeaturePackages(): void
    {
        // Arrange
        $currentReleaseGroup = new ReleaseGroupDto(
            1000,
            'test',
            new ModuleDtoCollection([
                new ModuleDto('spryker/package-one', '^4.17.1', 'minor', ['spryker-feature/feature-package-one' => 'dev-master as 20440.0']),
                new ModuleDto('spryker/package-two', '^4.17.1', 'minor', ['spryker-feature/feature-package-two' => 'dev-master as 20440.0']),
                new ModuleDto('spryker/package-three', '^4.17.1', 'minor', ['spryker-feature/feature-package-three' => 'dev-master as 20441.0']),
            ]),
            new ModuleDtoCollection(),
            new ModuleDtoCollection(),
            new DateTime(),
            false,
            'https://api.release.spryker.com/release-groups/view/1',
            100,
        );

        $stepsResponseDtoMock = $this->createMock(StepsResponseDto::class);
        $stepsResponseDtoMock->method('getCurrentReleaseGroup')->willReturn($currentReleaseGroup);
        $event = new ReleaseGroupProcessorEvent($stepsResponseDtoMock);
        $packageManagerAdapterMock = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapterMock->method('getComposerJsonFile')->willReturn([
            'require' => [
                'spryker/package-one' => '^4.17.0',
                'spryker/package-two' => '^4.17.0',
                'spryker/package-three' => '^4.17.0',
                'spryker-feature/feature-package-two' => '^4.18.0',
                'spryker-feature/feature-package-one' => '^4.19.0',
                'spryker-feature/feature-package-three' => 'dev-master as 20440.0',
            ],
            'require-dev' => [
                'spryker/package-1' => '^3.17.0',
                'spryker/package-2' => '^3.17.0',
                'spryker/package-3' => '^3.17.0',
            ],
        ]);

        $featureDevMasterPackageExpanderEventSubscriber = new FeatureDevMasterPackageExpanderEventSubscriber(
            $this->createConfigurationProviderMock(true),
            $packageManagerAdapterMock,
            true,
        );

        // Act
        $featureDevMasterPackageExpanderEventSubscriber->onPreRequire($event);

        //Assert
        $this->assertSame(2, $currentReleaseGroup->getFeaturePackages()->count());
        $this->assertSame('spryker-feature/feature-package-one', $currentReleaseGroup->getFeaturePackages()->toArray()[0]->getName());
        $this->assertSame('spryker-feature/feature-package-two', $currentReleaseGroup->getFeaturePackages()->toArray()[1]->getName());
    }

    /**
     * @param bool $releaseGroupIdExist
     *
     * @return \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    public function createConfigurationProviderMock(bool $releaseGroupIdExist = false): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProviderInterface::class);
        if ($releaseGroupIdExist) {
            $configurationProvider->method('getReleaseGroupId')->willReturn(1000);
        }

        return $configurationProvider;
    }
}
