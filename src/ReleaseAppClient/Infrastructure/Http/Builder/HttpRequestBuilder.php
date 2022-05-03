<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Infrastructure\Http\Builder;

use GuzzleHttp\Psr7\Request;
use ReleaseAppClient\Domain\Http\HttpRequestInterface;
use ReleaseAppClient\Infrastructure\Configuration\ConfigurationProvider;

class HttpRequestBuilder implements HttpRequestBuilderInterface
{
    /**
     * @var array<string, string>
     */
    protected const HTTP_HEADER_LIST = ['Content-Type' => 'application/json'];

    /**
     * @var \ReleaseAppClient\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $releaseAppConfig;

    /**
     * @param \ReleaseAppClient\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->releaseAppConfig = $configurationProvider;
    }

    /**
     * @param \ReleaseAppClient\Domain\Http\HttpRequestInterface $request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createGuzzleRequest(HttpRequestInterface $request): Request
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
