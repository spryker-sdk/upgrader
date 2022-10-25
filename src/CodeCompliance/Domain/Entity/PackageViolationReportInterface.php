<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Entity;

use SprykerSdk\SdkContracts\Report\Violation\PackageViolationReportInterface as SdkContractsPackageViolationReportInterface;

interface PackageViolationReportInterface extends SdkContractsPackageViolationReportInterface
{
    /**
     * Specification:
     * - Returns violation list for the package layer.
     *
     * @api
     *
     * @return array<\CodeCompliance\Domain\Entity\ViolationInterface>
     */
    public function getViolations(): array;

    /**
     * Specification:
     * - Returns violation list for the package files.
     *
     * @api
     *
     * @return array<string, array<\CodeCompliance\Domain\Entity\ViolationInterface>>
     */
    public function getFileViolations(): array;
}
