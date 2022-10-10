<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Entity;

use Exception;
use SprykerSdk\SdkContracts\Report\ReportInterface;
use SprykerSdk\SdkContracts\Report\Violation\PackageViolationReportInterface;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;
use SprykerSdk\SdkContracts\Report\Violation\ViolationReportInterface;

class Report implements ReportInterface, ViolationReportInterface
{
    /**
     * @var string
     */
    protected const KEY_VIOLATIONS = 'violations';

    /**
     * @var string
     */
    protected const KEY_PROJECT = 'project';

    /**
     * @var string
     */
    protected const KEY_PATH = 'path';

    /**
     * @var string
     */
    protected string $project = '';

    /**
     * @var string
     */
    protected string $path = '';

    /**
     * @var array<\CodeCompliance\Domain\Entity\Violation>
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
     * @param array<\CodeCompliance\Domain\Entity\Violation> $violations
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
     * @return array<\CodeCompliance\Domain\Entity\Violation>
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
     * @param array<\SprykerSdk\SdkContracts\Report\Violation\ViolationInterface> $violations
     *
     * @return bool
     */
    protected function hasErrorSeverity(array $violations): bool
    {
        foreach ($violations as $violation) {
            if ($violation->getSeverity() === ViolationInterface::SEVERITY_ERROR) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        $data[static::KEY_PROJECT] = $this->getProject();
        $data[static::KEY_PATH] = $this->getPath();

        $violations = [];
        foreach ($this->getViolations() as $violation) {
            $violations[] = $violation->toArray();
        }
        $data[static::KEY_VIOLATIONS] = $violations;

        return $data;
    }

    /**
     * @param array<mixed> $data
     *
     * @throws \Exception
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data[static::KEY_PROJECT])) {
            throw new Exception(sprintf('Key %s not found', static::KEY_PROJECT));
        }
        if (!isset($data[static::KEY_PATH])) {
            throw new Exception(sprintf('Key %s not found', static::KEY_PATH));
        }

        $report = new self($data[static::KEY_PROJECT], $data[static::KEY_PATH]);

        if (!is_array($data[static::KEY_VIOLATIONS])) {
            return $report;
        }

        foreach ($data[static::KEY_VIOLATIONS] as $violationData) {
            $report->addViolations([Violation::fromArray($violationData)]);
        }

        return $report;
    }
}
