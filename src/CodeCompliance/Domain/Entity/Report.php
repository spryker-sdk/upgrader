<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Entity;

use SprykerSdk\SdkContracts\Violation\PackageViolationReportInterface;
use SprykerSdk\SdkContracts\Violation\ViolationReportInterface;

class Report implements ViolationReportInterface
{
    /**
     * @var string
     */
    protected string $project = '';

    /**
     * @var string
     */
    protected string $path = '';

    /**
     * @var array<\SprykerSdk\SdkContracts\Violation\ViolationInterface>
     */
    protected array $violations = [];

    /**
     * @var array<\SprykerSdk\SdkContracts\Violation\PackageViolationReportInterface>
     */
    protected array $packages = [];

    /**
     * @param string $project
     * @param string $path
     */
    public function __construct(string $project = '', string $path = '')
    {
        $this->project = $project;
        $this->path = $path;
    }

    /**
     * @param array<\SprykerSdk\SdkContracts\Violation\ViolationInterface> $violations
     *
     * @return $this
     */
    public function addViolations(array $violations)
    {
        foreach ($violations as $violation) {
            $this->violations[] = $violation;
        }

        return $this;
    }

    /**
     * @return array<\SprykerSdk\SdkContracts\Violation\ViolationInterface>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @param \SprykerSdk\SdkContracts\Violation\PackageViolationReportInterface $package
     *
     * @return $this
     */
    public function addPackage(PackageViolationReportInterface $package)
    {
        $this->packages[] = $package;

        return $this;
    }

    /**
     * @return array<\SprykerSdk\SdkContracts\Violation\PackageViolationReportInterface>
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        if ($this->getViolations()) {
            return true;
        }

        foreach ($this->getPackages() as $package) {
            if ($package->getViolations()) {
                return true;
            }
            foreach ($package->getFileViolations() as $violations) {
                if ($violations) {
                    return true;
                }
            }
        }

        return false;
    }
}
