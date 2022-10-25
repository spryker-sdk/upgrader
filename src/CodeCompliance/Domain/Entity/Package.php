<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Entity;

class Package implements PackageViolationReportInterface
{
    /**
     * @var string
     */
    protected string $id = '';

    /**
     * @var string
     */
    protected string $package = '';

    /**
     * @var string
     */
    protected string $path = '';

    /**
     * @var array<\CodeCompliance\Domain\Entity\ViolationInterface>
     */
    protected array $violations = [];

    /**
     * @var array<string, array<\CodeCompliance\Domain\Entity\ViolationInterface>>
     */
    protected array $fileViolations = [];

    /**
     * @param string $id
     * @param string $path
     * @param string $package
     */
    public function __construct(string $id = '', string $path = '', string $package = '')
    {
        $this->id = $id;
        $this->path = $path;
        $this->package = $package;
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
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\ViolationInterface>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @return array<string, array<\CodeCompliance\Domain\Entity\ViolationInterface>>
     */
    public function getFileViolations(): array
    {
        return $this->fileViolations;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\ViolationInterface $violation
     *
     * @return $this
     */
    public function addViolation(ViolationInterface $violation)
    {
        $this->violations[] = $violation;

        return $this;
    }

    /**
     * @param string $file
     * @param \CodeCompliance\Domain\Entity\ViolationInterface $fileViolation
     *
     * @return $this
     */
    public function addFileViolation(string $file, ViolationInterface $fileViolation)
    {
        $this->fileViolations[$file][] = $fileViolation;

        return $this;
    }
}
