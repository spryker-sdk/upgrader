<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Event;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Event\UpgraderEventFactory;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class UpgraderEventFactoryTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testCreateUpgraderStartedEventShouldReturnStartedEvent(): void
    {
        // Arrange
        $organizationName = 'org';
        $repositoryName = 'repo';
        $ciExecutionId = 'executionId';
        $workspaceName = 'workspaceName';
        $configurationProviderMock = $this->createConfigurationProviderMock($organizationName, $repositoryName, $ciExecutionId, $workspaceName);
        $upgraderEventFactory = new UpgraderEventFactory($configurationProviderMock);

        // Act
        $event = $upgraderEventFactory->createUpgraderStartedEvent();

        // Assert
        $this->assertSame($organizationName, $event->getPayLoad()['organizationName']);
        $this->assertSame($repositoryName, $event->getPayLoad()['repositoryName']);
        $this->assertSame($ciExecutionId, $event->getPayLoad()['ciExecutionId']);
        $this->assertSame($workspaceName, $event->getPayLoad()['workspaceName']);
    }

    /**
     * @return void
     */
    public function testCreateUpgraderStartedEventShouldReturnStartedEventWithUuid(): void
    {
        // Arrange
        $organizationName = 'org';
        $repositoryName = 'repo';
        $ciExecutionId = '';
        $workspaceName = 'workspaceName';
        $configurationProviderMock = $this->createConfigurationProviderMock($organizationName, $repositoryName, $ciExecutionId, $workspaceName);
        $upgraderEventFactory = new UpgraderEventFactory($configurationProviderMock);

        // Act
        $event = $upgraderEventFactory->createUpgraderStartedEvent();

        // Assert
        $this->assertMatchesRegularExpression('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $event->getPayLoad()['ciExecutionId']);
    }

    /**
     * @return void
     */
    public function testCreateUpgraderFinishedEventShouldReturnFinishedClientEvent(): void
    {
        // Arrange
        $organizationName = 'org';
        $repositoryName = 'repo';
        $ciExecutionId = 'executionId';
        $workspaceName = 'workspaceName';
        $configurationProviderMock = $this->createConfigurationProviderMock($organizationName, $repositoryName, $ciExecutionId, $workspaceName);
        $upgraderEventFactory = new UpgraderEventFactory($configurationProviderMock);

        $stepsResponseDto = new StepsResponseDto();
        $stepsResponseDto->setError(Error::createClientCodeError('client_error'));

        $duration = 3;

        // Act
        $event = $upgraderEventFactory->createUpgraderFinishedEvent($stepsResponseDto, $duration);

        // Assert
        $payload = $event->getPayLoad();
        $this->assertSame($organizationName, $payload['organizationName']);
        $this->assertSame($repositoryName, $payload['repositoryName']);
        $this->assertSame($ciExecutionId, $payload['ciExecutionId']);
        $this->assertSame($workspaceName, $payload['workspaceName']);
        $this->assertTrue($payload['isClientIssue']);
        $this->assertSame('client_error', $payload['reason']);
    }

    /**
     * @return void
     */
    public function testCreateUpgraderFinishedEventShouldNonClientNonReasonEventWhenNoErrorRaised(): void
    {
        // Arrange
        $organizationName = 'org';
        $repositoryName = 'repo';
        $ciExecutionId = 'executionId';
        $workspaceName = 'workspaceName';
        $configurationProviderMock = $this->createConfigurationProviderMock($organizationName, $repositoryName, $ciExecutionId, $workspaceName);
        $upgraderEventFactory = new UpgraderEventFactory($configurationProviderMock);

        $stepsResponseDto = new StepsResponseDto();

        $duration = 3;

        // Act
        $event = $upgraderEventFactory->createUpgraderFinishedEvent($stepsResponseDto, $duration);

        // Assert
        $payload = $event->getPayLoad();
        $this->assertSame($organizationName, $payload['organizationName']);
        $this->assertSame($repositoryName, $payload['repositoryName']);
        $this->assertSame($ciExecutionId, $payload['ciExecutionId']);
        $this->assertSame($workspaceName, $payload['workspaceName']);
        $this->assertFalse($payload['isClientIssue']);
        $this->assertSame('', $payload['reason']);
    }

    /**
     * @return void
     */
    public function testCreateUpgraderFinishedEventContainsRgStatDataIfSet(): void
    {
        // Arrange
        $upgraderEventFactory = new UpgraderEventFactory($this->createConfigurationProviderMock());
        $stepsResponseDto = new StepsResponseDto();

        $expectedAvailableRgsAmount = 22;
        $expectedAppliedRgsAmount = 10;
        $expectedAppliedPackagesAmount = 18;
        $stepsResponseDto->getReleaseGroupStatDto()->setAvailableRgsAmount($expectedAvailableRgsAmount);
        $stepsResponseDto->getReleaseGroupStatDto()->setAppliedRGsAmount($expectedAppliedRgsAmount);
        $stepsResponseDto->getReleaseGroupStatDto()->setAppliedPackagesAmount($expectedAppliedPackagesAmount);

        // Act
        $event = $upgraderEventFactory->createUpgraderFinishedEvent($stepsResponseDto, 3);

        // Assert
        $payload = $event->getPayLoad();

        $this->assertSame(
            $expectedAvailableRgsAmount,
            $payload['availableRgsAmount'],
            'Event contains correct amount of available Release Groups.',
        );
        $this->assertSame(
            $expectedAppliedPackagesAmount,
            $payload['appliedPackages'],
            'Event contains correct amount of applied packages.',
        );
        $this->assertSame(
            $expectedAppliedRgsAmount,
            $payload['appliedRGs'],
            'Event contains correct amount of applied Release Groups.',
        );
    }

    /**
     * @return void
     */
    public function testCreateUpgraderFinishedEventContainsDefaultDataIfNotSet(): void
    {
        // Arrange
        $upgraderEventFactory = new UpgraderEventFactory($this->createConfigurationProviderMock());
        $stepsResponseDto = new StepsResponseDto();

        // Act
        $event = $upgraderEventFactory->createUpgraderFinishedEvent($stepsResponseDto, 3);

        // Assert
        $payload = $event->getPayLoad();

        $this->assertSame(
            0,
            $payload['availableRgsAmount'],
            'Event contains correct amount of available Release Groups.',
        );
        $this->assertSame(
            0,
            $payload['appliedPackages'],
            'Event contains correct amount of applied packages.',
        );
        $this->assertSame(
            0,
            $payload['appliedRGs'],
            'Event contains correct amount of applied Release Groups.',
        );
    }

    /**
     * @param string $organizationName
     * @param string $repositoryName
     * @param string $ciExecutionId
     * @param string $workspaceName
     *
     * @return \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected function createConfigurationProviderMock(
        string $organizationName = 'org',
        string $repositoryName = 'repo',
        string $ciExecutionId = 'executionId',
        string $workspaceName = 'workspaceName'
    ): ConfigurationProvider {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getOrganizationName')->willReturn($organizationName);
        $configurationProvider->method('getRepositoryName')->willReturn($repositoryName);
        $configurationProvider->method('getCiExecutionId')->willReturn($ciExecutionId);
        $configurationProvider->method('getCiWorkspaceName')->willReturn($workspaceName);

        return $configurationProvider;
    }
}
