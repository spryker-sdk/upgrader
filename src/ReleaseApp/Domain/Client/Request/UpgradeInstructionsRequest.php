<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Domain\Client\Request;

use ReleaseApp\Domain\Entities\UpgradeInstructions;

class UpgradeInstructionsRequest implements RequestInterface
{
    /**
     * @var string
     */
    protected const PACKAGES = 'packages';

    /**
     * @var array<string, string>
     */
    protected array $packages;

    /**
     * @param array<string, string> $packages
     */
    public function __construct(array $packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return (string)json_encode($this->getBodyArray());
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
        return null;
    }

    /**
     * @return array<mixed>
     */
    protected function getBodyArray(): array
    {
        return [
            self::PACKAGES => $this->packages,
        ];
    }
}
