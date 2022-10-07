<?php

namespace CodeCompliance\Domain\Entity;
use SprykerSdk\SdkContracts\Report\Violation\ViolationReportInterface;

interface ReportInterface extends ViolationReportInterface
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array;

    /**
     * @param array $data
     * @return $this
     */
    public function fromArray(array $data): self;
}
