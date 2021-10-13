<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Upgrader\Business\Exception\UpgraderException;
use Upgrader\UpgraderConfig;

class HttpRequestExecutor implements HttpRequestExecutorInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzleClient;

    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    /**
     * @param \GuzzleHttp\Client $guzzleClient
     * @param \Upgrader\UpgraderConfig $config
     */
    public function __construct(Client $guzzleClient, UpgraderConfig $config)
    {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function requestExecute(RequestInterface $request): ResponseInterface
    {
        $attempts = 0;
        $exception = null;
        $guzzleResponse = null;

        do {
            try {
                $guzzleResponse = $this->guzzleClient->send($request);
            } catch (ServerException $currentException) {
                $exception = $currentException;
                sleep($this->config->getHttpRetrieveRetryDelay());
            } finally {
                ++$attempts;
            }
        } while ($attempts < $this->config->getHttpRetrieveAttemptsCount() && $guzzleResponse == null);

        if ($guzzleResponse === null) {
            if ($exception) {
                throw $exception;
            }

            throw new UpgraderException('Http request error ' . $request->getUri());
        }

        return $guzzleResponse;
    }
}
