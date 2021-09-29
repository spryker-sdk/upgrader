<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http;

class HttpResponse implements HttpResponseInterface
{
    protected const RESULT_KEY = 'result';

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
    public function getBody(): ?array
    {
        return $this->bodyArray[static::RESULT_KEY];
    }
}
