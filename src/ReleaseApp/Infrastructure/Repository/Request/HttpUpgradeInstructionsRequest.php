<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository\Request;

use ReleaseApp\Domain\Client\Request\RequestInterface;

class HttpUpgradeInstructionsRequest implements HttpRequestInterface
{
    /**
     * @var string
     */
    protected const ENDPOINT = '/upgrade-instructions.json';

    /**
     * @var \ReleaseApp\Domain\Client\Request\RequestInterface
     */
    protected RequestInterface $domainRequest;

    /**
     * @param \ReleaseApp\Domain\Client\Request\RequestInterface $domainRequest
     */
    public function __construct(RequestInterface $domainRequest)
    {
        $this->domainRequest = $domainRequest;
    }

    /**
     * @return \ReleaseApp\Domain\Client\Request\RequestInterface
     */
    public function getDomainRequest(): RequestInterface
    {
        return $this->domainRequest;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return sprintf('%s?%s', static::ENDPOINT, $this->domainRequest->getParameters());
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return static::REQUEST_TYPE_POST;
    }
}
