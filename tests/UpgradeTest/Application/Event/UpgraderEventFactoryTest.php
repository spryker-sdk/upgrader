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
        $configurationProviderMock = $this->createConfigurationProviderMock($organizationName, $repositoryName, $ciExecutionId);
        $upgraderEventFactory = new UpgraderEventFactory($configurationProviderMock);

        // Act
        $event = $upgraderEventFactory->createUpgraderStartedEvent();

        // Assert
        $this->assertSame($organizationName, $event->getPayLoad()['organizationName']);
        $this->assertSame($repositoryName, $event->getPayLoad()['repositoryName']);
        $this->assertSame($ciExecutionId, $event->getPayLoad()['ciExecutionId']);
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
        $configurationProviderMock = $this->createConfigurationProviderMock($organizationName, $repositoryName, $ciExecutionId);
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
        $configurationProviderMock = $this->createConfigurationProviderMock($organizationName, $repositoryName, $ciExecutionId);
        $upgraderEventFactory = new UpgraderEventFactory($configurationProviderMock);

        $stepsResponseDto = new StepsResponseDto();
        $stepsResponseDto->setError(Error::createClientCodeError('client_error'));

        $duration = 3;

        // Act
        $event = $upgraderEventFactory->createUpgraderFinishedEvent($stepsResponseDto, $duration);

        // Assert
        $this->assertSame($organizationName, $event->getPayLoad()['organizationName']);
        $this->assertSame($repositoryName, $event->getPayLoad()['repositoryName']);
        $this->assertSame($ciExecutionId, $event->getPayLoad()['ciExecutionId']);
        $this->assertTrue($event->getPayLoad()['isClientIssue']);
        $this->assertSame('client_error', $event->getPayLoad()['reason']);
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
        $configurationProviderMock = $this->createConfigurationProviderMock($organizationName, $repositoryName, $ciExecutionId);
        $upgraderEventFactory = new UpgraderEventFactory($configurationProviderMock);

        $stepsResponseDto = new StepsResponseDto();

        $duration = 3;

        // Act
        $event = $upgraderEventFactory->createUpgraderFinishedEvent($stepsResponseDto, $duration);

        // Assert
        $this->assertSame($organizationName, $event->getPayLoad()['organizationName']);
        $this->assertSame($repositoryName, $event->getPayLoad()['repositoryName']);
        $this->assertSame($ciExecutionId, $event->getPayLoad()['ciExecutionId']);
        $this->assertFalse($event->getPayLoad()['isClientIssue']);
        $this->assertSame('', $event->getPayLoad()['reason']);
    }

    /**
     * @param string $organizationName
     * @param string $repositoryName
     * @param string $ciExecutionId
     *
     * @return \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    public function createConfigurationProviderMock(string $organizationName, string $repositoryName, string $ciExecutionId): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getOrganizationName')->willReturn($organizationName);
        $configurationProvider->method('getRepositoryName')->willReturn($repositoryName);
        $configurationProvider->method('getCiExecutionId')->willReturn($ciExecutionId);

        return $configurationProvider;
    }
}
