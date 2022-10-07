<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Entity;

use SprykerSdk\SdkContracts\Report\Violation\PackageViolationReportInterface;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;

class Report implements ReportInterface
{
    /**
     * @var string
     */
    protected const KEY_VIOLATIONS = 'violations';


    /**
     * @var string
     */
    protected string $project = '';

    /**
     * @var string
     */
    protected string $path = '';

    /**
     * @var array<\CodeCompliance\Domain\Entity\ViolationInterface>
     */
    protected array $violations = [];

    /**
     * @var array<\SprykerSdk\SdkContracts\Report\Violation\PackageViolationReportInterface>
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
     * @param array<\CodeCompliance\Domain\Entity\ViolationInterface> $violations
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
     * @return array<\CodeCompliance\Domain\Entity\ViolationInterface>
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
     * @param \SprykerSdk\SdkContracts\Report\Violation\PackageViolationReportInterface $package
     *
     * @return $this
     */
    public function addPackage(PackageViolationReportInterface $package)
    {
        $this->packages[] = $package;

        return $this;
    }

    /**
     * @return array<\SprykerSdk\SdkContracts\Report\Violation\PackageViolationReportInterface>
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
        if ($this->hasErrorSeverity($this->getViolations())) {
            return true;
        }

        foreach ($this->getPackages() as $package) {
            if ($this->hasErrorSeverity($package->getViolations())) {
                return true;
            }
            foreach ($package->getFileViolations() as $violations) {
                if ($this->hasErrorSeverity($violations)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array<\CodeCompliance\Domain\Entity\ViolationInterface> $violations
     * @return bool
     */
    protected function hasErrorSeverity(array $violations): bool
    {
        foreach ($violations as $violation) {
            if($violation->getSeverity() === ViolationInterface::SEVERITY_ERROR) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $violationReportStructure[static::KEY_VIOLATIONS] = [];

        foreach ($this->getViolations() as $violation) {
            $violationReportStructure[static::KEY_VIOLATIONS][] = $violation->toArray();
        }

        return $violationReportStructure;
    }

    public function fromArray(array $data): ReportInterface
    {
        // TODO: Implement fromArray() method.
    }


}
