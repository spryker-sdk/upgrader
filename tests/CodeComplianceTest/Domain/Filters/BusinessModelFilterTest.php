<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Filters;

use CodeCompliance\Domain\Checks\Filters\BusinessModelFilter;
use CodeComplianceTest\Domain\Checks\Filters\BaseFilterTest;

class BusinessModelFilterTest extends BaseFilterTest
{
    /**
     * @return array<string>
     */
    public function provideClassNamesData(): array
    {
        return [
            'Test/Test/TestClassName',
            'Test/Test/TestClassBusinessFactory',
            'Test/Test/TestConfig',
            'Test/Test/TestController',
            'Test/Test/TestService',
            'Test/Test/TestClient',
            'Test/Test/TestFacade',
            'Test/Test/TestEntityManager',
            'Test/Test/TestRepository',
            'Test/Test/TestPluginInterface',
            'Test/Test/TestServiceInterface',
            'Test/Test/TestClientInterface',
            'Test/Test/TestFacadeInterface',
            'Test/Test/TestEntityManagerInterface',
            'Test/Test/TestRepositoryInterface',
            'Test/Test/TestQueryContainerInterface',
            'Test/Test/TestWidget',
            'Test/Test/TestWidgetInterface',
            'Test/Test/TestPlugin',
            'Test/Test/TestPluginInterface',
            'Test/Test/TestEventSubscriber',
            'Test/Test/TestEventSubscriberInterface',
        ];
    }

    /**
     * @return void
     */
    public function testFilter(): void
    {
        // Arrange
        $businessFactoryFilter = new BusinessModelFilter();

        // Act
        $filteredSources = $businessFactoryFilter->filter($this->getCodebaseObjects());

        // Assert
        $this->assertNotEmpty($filteredSources);
        $this->assertCount(1, $filteredSources);
    }
}
