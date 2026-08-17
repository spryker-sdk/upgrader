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

    protected RequestInterface $request;

    public function __construct(RequestInterface $domainRequest)
    {
        $this->request = $domainRequest;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getEndpoint(): string
    {
        return sprintf('%s?%s', static::REQUEST_ENDPOINT, $this->request->getParameters());
    }

    public function getMethod(): string
    {
        return static::REQUEST_METHOD_POST;
    }
}
