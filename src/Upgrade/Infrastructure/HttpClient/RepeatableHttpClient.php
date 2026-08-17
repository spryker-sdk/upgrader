<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\HttpClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Upgrade\Infrastructure\Exception\RemoteServerUnreachableException;

class RepeatableHttpClient implements ClientInterface
{
    protected ClientInterface $client;

    protected int $maxAttempts;

    protected int $usecDelay;

    public function __construct(ClientInterface $client, int $maxAttempts, int $usecDelay = 0)
    {
        $this->client = $client;
        $this->maxAttempts = $maxAttempts;
        $this->usecDelay = $usecDelay;
    }

    /**
     * @throws \Upgrade\Infrastructure\Exception\RemoteServerUnreachableException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $currentAttempt = 0;

        while ($currentAttempt < $this->maxAttempts) {
            usleep($this->getDelayUSec($currentAttempt));

            $response = $this->doRequest($request);

            if ($response !== null) {
                return $response;
            }

            ++$currentAttempt;
        }

        throw new RemoteServerUnreachableException(sprintf('Server `%s` is unreachable.', $request->getUri()));
    }

    protected function getDelayUSec(int $tryAttempt): int
    {
        return $tryAttempt * $this->usecDelay;
    }

    protected function doRequest(RequestInterface $request): ?ResponseInterface
    {
        try {
            $response = $this->client->sendRequest($request);

            if ($response->getStatusCode() < 500) {
                return $response;
            }
        } catch (NetworkExceptionInterface $e) {
        }

        return null;
    }
}
