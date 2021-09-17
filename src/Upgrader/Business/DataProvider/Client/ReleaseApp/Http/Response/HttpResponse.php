<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response;

class HttpResponse implements HttpResponseInterface
{
    public const RESULT_KEY = 'result';

    /**
     * @var int
     */
    protected $code;

    /**
     * @var mixed
     */
    protected $bodyArray;

    /**
     * @param int $code
     * @param string $body
     */
    public function __construct(int $code, string $body)
    {
        $this->code = $code;
        $this->bodyArray = json_decode($body, true);
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->getCode();
    }

    /**
     * @return array|null
     */
    public function getBodyArray(): ?array
    {
        return $this->bodyArray[self::RESULT_KEY];
    }
}
