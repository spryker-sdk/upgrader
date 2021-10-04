<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder;

use GuzzleHttp\Psr7\Request;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestInterface;
use Upgrader\UpgraderConfig;

class HttpRequestBuilder implements HttpRequestBuilderInterface
{
    /**
     * @var array
     */
    protected const HTTP_HEADER_LIST = ['Content-Type' => 'application/json'];

    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $upgraderConfig;

    /**
     * @param \Upgrader\UpgraderConfig $upgraderConfig
     */
    public function __construct(UpgraderConfig $upgraderConfig)
    {
        $this->upgraderConfig = $upgraderConfig;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestInterface $request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createGuzzleRequest(HttpRequestInterface $request): Request
    {
        return new Request(
            $request->getMethod(),
            $this->getBaseUrl() . $request->getEndpoint(),
            static::HTTP_HEADER_LIST,
            $request->getBody()
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
