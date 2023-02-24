<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Upgrade\Application\Service\UpgradeService;
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

        /** @var \Upgrade\Application\Strategy\StrategyResolver $strategyResolver */
        $strategyResolver = static::bootKernel()->getContainer()->get(StrategyResolver::class);

        $service = new UpgradeService($configurationProviderMock, $strategyResolver);
        $res = $service->upgrade();

        $this->assertFalse($res->isSuccessful());
        $this->assertSame(<<<OUIPUT
Start executing "Check credentials" step
Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.
Step is failed. It will be reapplied with a fixer
Step is failed
OUIPUT
, $res->getOutputMessage());
    }
}
