<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Domain\Client\Request;

use ReleaseApp\Domain\Entities\UpgradeInstruction;

class UpgradeReleaseGroupInstructionsRequest implements RequestInterface
{
    /**
     * @var int
     */
    private int $releaseGroupId;

    /**
     * @param int $releaseGroupId
     */
    public function __construct(int $releaseGroupId)
    {
        $this->releaseGroupId = $releaseGroupId;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getResponseClass(): string
    {
        return UpgradeInstruction::class;
    }

    /**
     * @return string
     */
    public function getParameters(): string
    {
        return sprintf('%s=%s', 'release-group-id', $this->releaseGroupId);
    }
}
