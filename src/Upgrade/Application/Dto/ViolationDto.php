<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class ViolationDto
{
    /**
     * @var string
     */
    protected string $message;

    /**
     * @var string
     */
    protected string $target;

    /**
     * @var string
     */
    protected string $package;

    /**
     * @var array<mixed>
     */
    protected array $additionalData;

    /**
     * @param string $message
     * @param string $target
     * @param string $package
     * @param array<mixed> $additionalData
     */
    public function __construct(string $message, string $target = '', string $package = '', array $additionalData = [])
    {
        $this->message = $message;
        $this->target = $target;
        $this->additionalData = $additionalData;
        $this->package = $package;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @return string
     */
    public function getPackage(): string
    {
        return $this->package;
    }

    /**
     * @return array<mixed>
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
}
