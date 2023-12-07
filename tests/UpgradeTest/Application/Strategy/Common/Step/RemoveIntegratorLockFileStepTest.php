<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common\Step;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Utils\Infrastructure\Service\Filesystem;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\Step\RemoveIntegratorLockFileStep;

class RemoveIntegratorLockFileStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testRun(): void
    {
        // Arrange & Assert
        $filesystemMock = $this->createMock(Filesystem::class);
        $filesystemMock->expects($this->once())->method('remove');

        $vcsMock = $this->createMock(VersionControlSystemAdapterInterface::class);

        $removeIntegratorLockFileStep = new RemoveIntegratorLockFileStep($vcsMock, $filesystemMock);

        // Act
        $removeIntegratorLockFileStep->run(new StepsResponseDto());
    }
}
