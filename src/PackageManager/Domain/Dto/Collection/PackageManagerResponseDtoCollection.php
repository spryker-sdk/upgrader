<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Dto\Collection;

use PackageManager\Domain\Dto\ResponseDto;

class PackageManagerResponseDtoCollection
{
    /**
     * @var array<\PackageManager\Domain\Dto\ResponseDto>
     */
    protected $elements = [];

    /**
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \PackageManager\Domain\Dto\ResponseDto $element
     *
     * @return void
     */
    public function add(ResponseDto $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->isSuccess() ? ResponseDto::CODE_SUCCESS : ResponseDto::CODE_ERROR;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        foreach ($this->elements as $result) {
            if (!$result->isSuccess()) {
                return false;
            }
        }

        return true;
    }
}
