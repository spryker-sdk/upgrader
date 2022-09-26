<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Domain\Client\Response;

class Response implements ResponseInterface
{
    /**
     * @var string
     */
    protected const RESULT_KEY = 'result';

    /**
     * @var int
     */
    protected int $code;

    /**
     * @var array<mixed>
     */
    protected array $body;

    /**
     * @param int $code
     * @param string $body
     */
    public function __construct(int $code, string $body)
    {
        $this->code = $code;
        $this->body = json_decode($body, true);
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array<mixed>|null
     */
    public function getBody(): ?array
    {
        return $this->body[static::RESULT_KEY];
    }
}
