<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Client\Request;

use ReleaseApp\Domain\Entities\UpgradeInstructions;

class UpgradeInstructionsRequest implements RequestInterface
{
    /**
     * @var int
     */
    protected int $idModuleVersion;

    /**
     * @param int $moduleVersionId
     */
    public function __construct(int $moduleVersionId)
    {
        $this->idModuleVersion = $moduleVersionId;
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
        return UpgradeInstructions::class;
    }

    /**
     * @return string|null
     */
    public function getParameters(): ?string
    {
        return sprintf('%s=%s', 'module_version_id', $this->idModuleVersion);
    }
}
