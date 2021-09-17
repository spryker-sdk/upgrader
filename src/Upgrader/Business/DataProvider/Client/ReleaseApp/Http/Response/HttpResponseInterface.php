<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response;

interface HttpResponseInterface
{
    /**
     * @param int $code
     * @param string $body
     */
    public function __construct(int $code, string $body);

    /**
     * @return int
     */
    public function getCode(): int;

    /**
     * @return array|null
     */
    public function getBodyArray(): ?array;
}
