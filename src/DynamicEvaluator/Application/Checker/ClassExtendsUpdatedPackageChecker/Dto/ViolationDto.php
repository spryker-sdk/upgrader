<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto;

use Upgrade\Application\Dto\ViolationDtoInterface;

class ViolationDto implements ViolationDtoInterface
{
    protected string $message;

    protected string $target;

    protected string $package;

    /**
     * @var array<mixed>
     */
    protected array $additionalData;

    protected string $hash;

    /**
     * @param array<mixed> $additionalData
     */
    public function __construct(string $message, string $target = '', string $package = '', array $additionalData = [])
    {
        $this->message = $message;
        $this->target = $target;
        $this->additionalData = $additionalData;
        $this->package = $package;
        $this->hash = sha1($message . $target . $package);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function equals(ViolationDtoInterface $violationDto): bool
    {
        return $violationDto instanceof ViolationDto && $this->getHash() === $violationDto->getHash();
    }
}
