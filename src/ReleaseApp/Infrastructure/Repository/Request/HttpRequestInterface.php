<?php

namespace ReleaseApp\Infrastructure\Repository\Request;

use ReleaseApp\Domain\Client\Request\RequestInterface;

interface HttpRequestInterface
{
    /**
     * @var string
     */
    public const REQUEST_TYPE_POST = 'POST';

    /**
     * @var string
     */
    public const REQUEST_TYPE_GET = 'GET';

    /**
     * @return RequestInterface
     */
    public function getDomainRequest(): RequestInterface;

    /**
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * @return string
     */
    public function getMethod(): string;
}
