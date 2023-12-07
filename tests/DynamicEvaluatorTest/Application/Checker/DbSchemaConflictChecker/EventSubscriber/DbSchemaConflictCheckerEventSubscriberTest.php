<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\DbSchemaConflictChecker\EventSubscriber;

use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\DbSchemaConflictChecker;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\EventSubscriber\DbSchemaConflictCheckerEventSubscriber;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class DbSchemaConflictCheckerEventSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPostRequireShouldBeSkippedWhenEvaluatorDisables(): void
    {
        // Arrange & Assert
        $subscriber = new DbSchemaConflictCheckerEventSubscriber(
            $this->createDbSchemaConflictCheckerMock(),
            $this->createConfigurationProviderMock(false),
        );

        // Act
        $subscriber->onPostRequire(new ReleaseGroupProcessorPostRequireEvent(new StepsResponseDto(), new PackageManagerResponseDto(true)));
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldAddViolationsToStepsResponse(): void
    {
        // Arrange
        $subscriber = new DbSchemaConflictCheckerEventSubscriber(
            $this->createDbSchemaConflictCheckerMock([new ViolationDto('spy_acl.schema.xml', 'table', ['column'])]),
            $this->createConfigurationProviderMock(),
        );

        $stepsResponse = new StepsResponseDto();

        // Act
        $subscriber->onPostRequire(new ReleaseGroupProcessorPostRequireEvent($stepsResponse, new PackageManagerResponseDto(true)));

        // Assert
        $this->assertCount(1, $stepsResponse->getViolations());
    }

    /**
     * @return void
     */
    public function testGetSubscribedEventsShouldReturnEventsDeclaration(): void
    {
        $this->assertSame(
            [ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE => 'onPostRequire'],
            DbSchemaConflictCheckerEventSubscriber::getSubscribedEvents(),
        );
    }

    /**
     * @param array<\DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\Dto\ViolationDto> $violations
     *
     * @return \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\DbSchemaConflictChecker
     */
    protected function createDbSchemaConflictCheckerMock(array $violations = []): DbSchemaConflictChecker
    {
        $dbSchemaConflictChecker = $this->createMock(DbSchemaConflictChecker::class);
        $dbSchemaConflictChecker
            ->expects($violations ? $this->once() : $this->never())
            ->method('check')
            ->willReturn($violations);

        return $dbSchemaConflictChecker;
    }

    /**
     * @param bool $isEvaluatorEnabled
     *
     * @return \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    public function createConfigurationProviderMock(bool $isEvaluatorEnabled = true): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProviderInterface::class);
        $configurationProvider->method('isEvaluatorEnabled')->willReturn($isEvaluatorEnabled);

        return $configurationProvider;
    }
}
