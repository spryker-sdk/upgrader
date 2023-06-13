<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Client\Request;

use ReleaseApp\Domain\Client\Request\RequestInterface;

class HttpUpgradeReleaseGroupInstructionsRequest implements HttpRequestInterface
{
    /**
     * @var string
     */
    public const REQUEST_ENDPOINT = '/upgrade-release-group-instructions.json';

    /**
     * @var \ReleaseApp\Domain\Client\Request\RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @param \ReleaseApp\Domain\Client\Request\RequestInterface $domainRequest
     */
    public function __construct(RequestInterface $domainRequest)
    {
        $this->request = $domainRequest;
    }

    /**
     * @return \ReleaseApp\Domain\Client\Request\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return sprintf('%s?%s', static::REQUEST_ENDPOINT, $this->request->getParameters());
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return static::REQUEST_METHOD_POST;
    }
}
