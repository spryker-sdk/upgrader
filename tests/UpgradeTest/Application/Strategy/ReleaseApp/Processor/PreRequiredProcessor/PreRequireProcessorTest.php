<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor\PreRequireProcessor;
use Upgrade\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor\PreRequireProcessorStrategyInterface;

class PreRequireProcessorTest extends TestCase
{
    /**
     * @return void
     */
    public function testProcessShouldInvokeAllStrategies(): void
    {
        // Arrange
        $releaseGroupDtoCollection = new ReleaseGroupDtoCollection();
        $preRequireProcessor = new PreRequireProcessor([$this->createPreRequireProcessorStrategyMock($releaseGroupDtoCollection)]);

        // Act
        $preRequireProcessor->process($releaseGroupDtoCollection);
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $releaseGroupDtoCollection
     *
     * @return \Upgrade\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor\PreRequireProcessorStrategyInterface
     */
    protected function createPreRequireProcessorStrategyMock(ReleaseGroupDtoCollection $releaseGroupDtoCollection): PreRequireProcessorStrategyInterface
    {
        $preRequireProcessorStrategy = $this->createMock(PreRequireProcessorStrategyInterface::class);
        $preRequireProcessorStrategy->expects($this->once())->method('process')->with($releaseGroupDtoCollection);

        return $preRequireProcessorStrategy;
    }
}
