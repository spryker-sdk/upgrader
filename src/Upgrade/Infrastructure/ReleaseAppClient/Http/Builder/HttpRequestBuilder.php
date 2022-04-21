<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http\Builder;

use GuzzleHttp\Psr7\Request;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\ReleaseAppClient\Http\HttpRequestInterface;

class HttpRequestBuilder implements HttpRequestBuilderInterface
{
    /**
     * @var array<string, string>
     */
    protected const HTTP_HEADER_LIST = ['Content-Type' => 'application/json'];

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected $upgraderConfig;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $upgraderConfig
     */
    public function __construct(ConfigurationProvider $upgraderConfig)
    {
        $this->upgraderConfig = $upgraderConfig;
    }

    /**
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\HttpRequestInterface $request
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
        return $this->upgraderConfig->getReleaseAppUrl();
    }
}
