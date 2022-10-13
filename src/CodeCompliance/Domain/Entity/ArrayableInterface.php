<?php

namespace CodeCompliance\Domain\Entity;

interface ArrayableInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * @param array<mixed> $data
     *
     * @return self
     */
    public static function fromArray(array $data): self;
}
