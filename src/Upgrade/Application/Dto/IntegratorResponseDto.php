<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class IntegratorResponseDto
{
    /**
     * @var string
     */
    protected const MESSAGES_KEY = 'message-list';

    /**
     * @var string
     */
    protected const WARNINGS_KEY = 'warning-list';

    /**
     * @var array<mixed>
     */
    protected array $data = [];

    /**
     * @param array<mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return array<string>
     */
    public function getMessages(): array
    {
        if (!is_array($this->data[static::MESSAGES_KEY])) {
            return [];
        }

        return $this->data[static::MESSAGES_KEY];
    }

    /**
     * @return array<string>
     */
    public function getWarnings(): array
    {
        if (!is_array($this->data[static::WARNINGS_KEY])) {
            return [];
        }

        return $this->data[static::WARNINGS_KEY];
    }
}
