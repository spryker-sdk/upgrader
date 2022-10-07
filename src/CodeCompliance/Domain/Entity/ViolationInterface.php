<?php

namespace CodeCompliance\Domain\Entity;

use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface as SdkContractsViolationInterface;

interface ViolationInterface extends SdkContractsViolationInterface
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
