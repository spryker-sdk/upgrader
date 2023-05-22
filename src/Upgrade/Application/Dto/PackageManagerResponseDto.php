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
     * @param bool $isSuccessful
     * @param string|null $outputMessage
     * @param array<string> $executedCommands
     */
    public function __construct(bool $isSuccessful, ?string $outputMessage = null, array $executedCommands = [])
    {
        parent::__construct($isSuccessful, $outputMessage);
        $this->executedCommands = $executedCommands;
    }

    /**
     * @return array<string>
     */
    public function getExecutedCommands(): array
    {
        return $this->executedCommands;
    }
}
