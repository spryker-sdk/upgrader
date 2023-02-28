<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Service;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Infrastructure\Client\HttpRequestExecutor;
use ReleaseApp\Infrastructure\Client\Request\HttpUpgradeAnalysisHttpRequest;
use ReleaseApp\Infrastructure\Client\Request\HttpUpgradeInstructionsRequest;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReleaseAppServiceTest extends KernelTestCase
{
    /**
     * @var string
     */
    protected const API_RESPONSE_DIR = 'tests/data/ReleaseApp/Api/Response';

    /**
     * @return void
     */
    public function testGetNewReleaseGroupsSuccess(): void
    {
        // Arrange
        $container = static::bootKernel()->getContainer();
        $container->set(HttpRequestExecutor::class, $this->createRequestExecutorMock());
        $request = new UpgradeAnalysisRequest('project-name', [], []);

        // Act
        $releaseGroups = $container->get(ReleaseAppService::class)->getNewReleaseGroups($request);

        // Assert
        $this->assertEquals(
            new ReleaseAppResponse(
                new ReleaseGroupDtoCollection([
                        new ReleaseGroupDto(
                            'FRW-229 Replace Swiftmailer dependency with SymfonyMailer',
                            new ModuleDtoCollection([
                                new ModuleDto('spryker/mail-extension', '1.0.0', 'major'),
                                new ModuleDto('spryker/symfony-mailer', '1.0.2', 'major'),
                            ]),
                            true,
                            'https://api.release.spryker.com/release-groups/view/4395',
                            true,
                        ),
                    ]),
            ),
            $releaseGroups,
        );
    }

    /**
     * @return \ReleaseApp\Infrastructure\Client\HttpRequestExecutor
     */
    protected function createRequestExecutorMock(): HttpRequestExecutor
    {
        $callback = function (Request $request) {
            $path = $request->getUri()->getPath();
            if ($path === HttpUpgradeAnalysisHttpRequest::REQUEST_ENDPOINT) {
                return $this->createHttpResponse(HttpUpgradeAnalysisHttpRequest::REQUEST_ENDPOINT);
            }

            if ($path === HttpUpgradeInstructionsRequest::REQUEST_ENDPOINT) {
                return $this->createHttpResponse(HttpUpgradeInstructionsRequest::REQUEST_ENDPOINT);
            }

            return null;
        };

        $executorMock = $this->createMock(HttpRequestExecutor::class);
        $executorMock->expects($this->any())
            ->method('execute')
            ->will($this->returnCallback($callback));

        return $executorMock;
    }

    /**
     * @param string $endpoint
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function createHttpResponse(string $endpoint): Response
    {
        $contents = file_get_contents(self::API_RESPONSE_DIR . $endpoint);

        return new Response(200, [], $contents);
    }
}
