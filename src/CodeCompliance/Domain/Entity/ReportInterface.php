<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Entity;

use SprykerSdk\SdkContracts\Report\ReportInterface as SdkContractsReportInterface;
use SprykerSdk\SdkContracts\Report\Violation\ViolationReportInterface;

interface ReportInterface extends SdkContractsReportInterface, ViolationReportInterface
{
    /**
     * @return array<\CodeCompliance\Domain\Entity\ViolationInterface>
     */
    public function getViolations(): array;

    /**
     * @return bool
     */
    public function hasError(): bool;

    /**
     * @return array<mixed>
     */
    public function toArray(): array;

    /**
     * @param array<mixed> $data
     *
     * @throws \Exception
     *
     * @return self
     */
    public static function fromArray(array $data): self;
}
