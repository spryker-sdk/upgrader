<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure;

use PHPUnit\Framework\TestCase;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzurePullRequestDescriptionNormalizer;

class AzurePullRequestDescriptionNormalizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testNormalizeShouldLeaveDescriptionAsIsWhenItLessThenMaxLength(): void
    {
        // Arrange
        $normalizer = new AzurePullRequestDescriptionNormalizer();

        // Act
        $description = $normalizer->normalize($this->getDescription(), '4ab87392-bc3c-11ed-afa1-0242ac120002');

        // Assert
        $this->assertSame($this->getDescription(), $description);
    }

    /**
     * @return void
     */
    public function testNormalizeShouldReturnCutDescriptionWhenInTooLong(): void
    {
        // Arrange
        $maxDescriptionLength = 100;
        $normalizer = new AzurePullRequestDescriptionNormalizer($maxDescriptionLength);

        // Act
        $description = $normalizer->normalize($this->getDescription(), '4ab87392-bc3c-11ed-afa1-0242ac120002');

        // Assert
        $this->assertSame(
            <<<DSCR
        Auto created via Upgrader tool.

        #### Overview
        Report ID: 096f66c9-7588-449d-aebb-95621f283619


        DSCR,
            $description,
        );
    }

    /**
     * @return void
     */
    public function testNormalizeShouldReturnDefaultDescriptionWhenInTooLong(): void
    {
        // Arrange
        $maxDescriptionLength = 30;
        $normalizer = new AzurePullRequestDescriptionNormalizer($maxDescriptionLength);

        // Act
        $description = $normalizer->normalize($this->getDescription(), '4ab87392-bc3c-11ed-afa1-0242ac120002');

        // Assert
        $this->assertSame(
            <<<DSCR
        Auto created via Upgrader tool.

        #### Overview
        Report ID: 4ab87392-bc3c-11ed-afa1-0242ac120002


        DSCR,
            $description,
        );
    }

    /**
     * @return string
     */
    protected function getDescription(): string
    {
        return <<<DSCR
        Auto created via Upgrader tool.

        #### Overview
        Report ID: 096f66c9-7588-449d-aebb-95621f283619

        **Packages upgraded:**
        Some long text
        DSCR;
    }
}
