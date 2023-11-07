<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Service;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Infrastructure\Client\HttpRequestExecutor;
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
        $request = new UpgradeInstructionsRequest([]);

        // Act
        $releaseGroups = $container->get(ReleaseAppService::class)->getNewReleaseGroups($request);

        // Assert
        $this->assertEquals(
            new ReleaseAppResponse(
                new ReleaseGroupDtoCollection([
                    new ReleaseGroupDto(
                        4395,
                        'FRW-229 Replace Swiftmailer dependency with SymfonyMailer',
                        new ModuleDtoCollection([
                            new ModuleDto('spryker/mail-extension', '1.0.0', 'major'),
                            new ModuleDto('spryker/symfony-mailer', '1.0.2', 'major'),
                        ]),
                        true,
                        'https://api.release.spryker.com/release-group/4395',
                        100,
                        true,
                    ),
                ]),
            ),
            $releaseGroups,
        );
    }

    /**
     * @return void
     */
    public function testGetReleaseGroupSuccess(): void
    {
        // Arrange
        $container = static::bootKernel()->getContainer();
        $container->set(HttpRequestExecutor::class, $this->createRequestExecutorMock());
        $request = new UpgradeReleaseGroupInstructionsRequest(4395);

        // Act
        $releaseGroups = $container->get(ReleaseAppService::class)->getReleaseGroup($request);

        // Assert
        $releaseGroupDto = new ReleaseGroupDto(
            4821,
            'CC-26540 Introduced the Shipment Types BAPI',
            new ModuleDtoCollection([
                new ModuleDto('spryker/shipment-types-backend-api', '0.1.0', 'minor'),
                new ModuleDto('spryker/shipment-type', '0.1.1', 'patch'),
            ]),
            true,
            'https://api.release.spryker.com/release-group/4821',
            100,
            false,
        );
        $releaseGroupDto->setJiraIssue('CC-25420');
        $releaseGroupDto->setJiraIssueLink('https://spryker.atlassian.net/browse/CC-25420');

        $this->assertEquals(
            new ReleaseAppResponse(
                new ReleaseGroupDtoCollection([
                    $releaseGroupDto,
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
            return $this->createHttpResponse($request->getUri()->getPath());
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
