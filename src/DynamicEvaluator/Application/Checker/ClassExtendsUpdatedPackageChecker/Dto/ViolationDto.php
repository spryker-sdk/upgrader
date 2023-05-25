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
     * @var string
     */
    protected string $hash;

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
        $this->hash = sha1($message . $target . $package);
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
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param \Upgrade\Application\Dto\ViolationDtoInterface $violationDto
     *
     * @return bool
     */
    public function equals(ViolationDtoInterface $violationDto): bool
    {
        return $violationDto instanceof ViolationDto && $this->getHash() === $violationDto->getHash();
    }
}
