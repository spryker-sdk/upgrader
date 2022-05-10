<?php

namespace ReleaseApp\Infrastructure\Repository\Request;


use ReleaseApp\Domain\Client\Request\RequestInterface;

class HttpUpgradeAnalysisHttpRequest implements HttpRequestInterface
{
    /**
     * @var string
     */
    protected const ENDPOINT = '/upgrade-analysis.json';

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
        return static::ENDPOINT;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return static::REQUEST_TYPE_POST;
    }
}
