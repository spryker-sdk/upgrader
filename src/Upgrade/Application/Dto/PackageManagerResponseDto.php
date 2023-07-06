<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class PackageManagerResponseDto extends ResponseDto
{
    /**
     * @var array<string>
     */
    protected array $executedCommands;

    /**
     * @var int
     */
    protected int $appliedPackagesAmount;

    /**
     * @param bool $isSuccessful
     * @param string|null $outputMessage
     * @param array<string> $executedCommands
     * @param int $appliedPackagesAmount
     */
    public function __construct(
        bool $isSuccessful,
        ?string $outputMessage = null,
        array $executedCommands = [],
        int $appliedPackagesAmount = 0
    ) {
        parent::__construct($isSuccessful, $outputMessage);
        $this->executedCommands = $executedCommands;
        $this->appliedPackagesAmount = $appliedPackagesAmount;
    }

    /**
     * @return array<string>
     */
    public function getExecutedCommands(): array
    {
        return $this->executedCommands;
    }

    /**
     * @return int
     */
    public function getAppliedPackagesAmount(): int
    {
        return $this->appliedPackagesAmount;
    }
}
