<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Upgrade\Application\Event\UpgraderEventFactory;
use Upgrade\Application\Service\UpgradeService;
use Upgrade\Application\Strategy\StrategyInterface;
use Upgrade\Application\Strategy\StrategyResolver;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class UpgradeServiceTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testUpgradeWithoutAccessToken(): void
    {
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $configurationProviderMock->method('getUpgradeStrategy')->willReturn('composer');
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $upgraderEventFactory = $this->createMock(UpgraderEventFactory::class);

        /** @var \Upgrade\Application\Strategy\StrategyResolver $strategyResolver */
        $strategyResolver = static::bootKernel()->getContainer()->get(StrategyResolver::class);

        $logger = $this->createMock(LoggerInterface::class);

        $service = new UpgradeService($configurationProviderMock, $strategyResolver, $logger, $eventDispatcherMock, $upgraderEventFactory);
        $res = $service->upgrade();

        $this->assertFalse($res->isSuccessful());
        $this->assertSame(
            <<<OUIPUT
        Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.
        Step `Check credentials` is failed
        OUIPUT,
            $res->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testUpgradeShouldCollectExceptionWhenItThrows(): void
    {
        $this->expectException(Exception::class);

        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $configurationProviderMock->method('getUpgradeStrategy')->willReturn('composer');
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $upgraderEventFactory = $this->createMock(UpgraderEventFactory::class);

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->method('upgrade')->willThrowException(new Exception('some_error'));

        $strategyResolver = $this->createMock(StrategyResolver::class);
        $strategyResolver->method('getStrategy')->willReturn($strategy);

        $logger = $this->createMock(LoggerInterface::class);

        $service = new UpgradeService($configurationProviderMock, $strategyResolver, $logger, $eventDispatcherMock, $upgraderEventFactory);
        $service->upgrade();
    }

    /**
     * @return \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
     */
    public function createEventDispatcherMock(): EventDispatcherInterface
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(2))->method('dispatch');

        return $eventDispatcher;
    }
}
