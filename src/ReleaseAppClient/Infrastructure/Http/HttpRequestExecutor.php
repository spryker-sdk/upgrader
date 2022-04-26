<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Infrastructure\Http;

use GuzzleHttp\Client as GuzzleHttp;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReleaseAppClient\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\UpgraderException;

class HttpRequestExecutor implements HttpRequestExecutorInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzleClient;

    /**
     * @var \ReleaseAppClient\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $config;

    /**
     * @param \ReleaseAppClient\Infrastructure\Configuration\ConfigurationProvider $config
     */
    public function __construct(ConfigurationProvider $config)
    {
        $this->guzzleClient = new GuzzleHttp();

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
