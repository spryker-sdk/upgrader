<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Entity;

use SprykerSdk\SdkContracts\Report\Violation\PackageViolationReportInterface;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;

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
     * @var array<\SprykerSdk\SdkContracts\Report\Violation\ViolationInterface>
     */
    protected array $violations = [];

    /**
     * @var array<string, array<\SprykerSdk\SdkContracts\Report\Violation\ViolationInterface>>
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
     * @return array<\SprykerSdk\SdkContracts\Report\Violation\ViolationInterface>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @return array<string, array<\SprykerSdk\SdkContracts\Report\Violation\ViolationInterface>>>
     */
    public function getFileViolations(): array
    {
        return $this->fileViolations;
    }

    /**
     * @param \SprykerSdk\SdkContracts\Report\Violation\ViolationInterface $violation
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
     * @param \SprykerSdk\SdkContracts\Report\Violation\ViolationInterface $fileViolation
     *
     * @return $this
     */
    public function addFileViolation(string $file, ViolationInterface $fileViolation)
    {
        $this->fileViolations[$file][] = $fileViolation;

        return $this;
    }
}
