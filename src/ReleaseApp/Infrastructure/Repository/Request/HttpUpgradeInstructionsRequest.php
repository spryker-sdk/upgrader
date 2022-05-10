<?php

namespace ReleaseApp\Infrastructure\Repository\Request;

use ReleaseApp\Domain\Client\Request\RequestInterface;

class HttpUpgradeInstructionsRequest implements HttpRequestInterface
{
    /**
     * @var string
     */
    protected const ENDPOINT = '/upgrade-instructions.json';

    /**
     * @var RequestInterface
     */
    protected RequestInterface $domainRequest;

    /**
     * @param RequestInterface $domainRequest
     */
    public function __construct(RequestInterface $domainRequest)
    {
        $this->domainRequest = $domainRequest;
    }

    /**
     * @return RequestInterface
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
