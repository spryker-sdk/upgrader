<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository\Builder;

use GuzzleHttp\Psr7\Request;
use ReleaseApp\Domain\Entities\RequestInterface;
use ReleaseApp\Infrastructure\Configuration\ConfigurationProvider;

class HttpRequestBuilder implements HttpRequestBuilderInterface
{
    /**
     * @var array<string, string>
     */
    protected const HTTP_HEADER_LIST = ['Content-Type' => 'application/json'];

    /**
     * @var \ReleaseApp\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $releaseAppConfig;

    /**
     * @param \ReleaseApp\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->releaseAppConfig = $configurationProvider;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\RequestInterface $request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createGuzzleRequest(RequestInterface $request): Request
    {
        return new Request(
            $request->getMethod(),
            $this->getBaseUrl() . $request->getEndpoint(),
            static::HTTP_HEADER_LIST,
            $request->getBody(),
        );
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return $this->releaseAppConfig->getReleaseAppUrl();
    }
}
