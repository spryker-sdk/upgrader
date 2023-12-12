<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\PublicApiFilePathsProvider;

use PHPUnit\Framework\TestCase;

class PublicApiFilePathsProviderTest extends TestCase
{
    /**
     * @dataProvider publicApiFilesDataProvider
     *
     * @param string $filePath
     *
     * @return void
     */
    public function testGetPublicApiFilePathsRegexCollectionShouldMatchApiPattern(string $filePath): void
    {
        // Arrange
        $publicApiFilePathsProvider = new PublicApiFilePathsProvider();

        // Act
        $result = $this->checkByRegexCollection($filePath, $publicApiFilePathsProvider->getPublicApiFilePathsRegexCollection());

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @dataProvider nonPublicApiFilesDataProvider
     *
     * @param string $filePath
     *
     * @return void
     */
    public function testGetPublicApiFilePathsRegexCollectionShouldNotMatchPattern(string $filePath): void
    {
        // Arrange
        $publicApiFilePathsProvider = new PublicApiFilePathsProvider();

        // Act
        $result = $this->checkByRegexCollection($filePath, $publicApiFilePathsProvider->getPublicApiFilePathsRegexCollection());

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @param string $filePath
     * @param array $regexCollection
     *
     * @return bool
     */
    protected function checkByRegexCollection(string $filePath, array $regexCollection): bool
    {
        foreach ($regexCollection as $regex) {
            if (preg_match(sprintf('#%s#', $regex), $filePath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<array<string>>
     */
    public function publicApiFilesDataProvider(): array
    {
        return [
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/PriceProductConfig.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/PriceProductDependencyProvider.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Business/PriceProductBusinessFactory.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Business/PriceProductFacade.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Business/PriceProductFacadeInterface.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Communication/AnyFile.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Dependency/AnyFile.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Persistence/PriceProductEntityManager.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Persistence/PriceProductEntityManagerInterface.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Persistence/PriceProductQueryContainer.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Persistence/PriceProductQueryContainerInterface.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Persistence/PriceProductRepository.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Persistence/PriceProductRepositoryInterface.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Presentation/AnyFile.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Shared/AnyFile.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Service/PriceProduct/PriceProductConfig.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Service/PriceProduct/PriceProductDependencyProvider.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Service/PriceProduct/PriceProductService.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Service/PriceProduct/PriceProductServiceInterface.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Service/PriceProduct/PriceProductServiceFactory.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Client/PriceProduct/PriceProductClient.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Client/PriceProduct/PriceProductClientInterface.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Client/PriceProduct/PriceProductConfig.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Client/PriceProduct/PriceProductDependencyProvider.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Client/PriceProduct/PriceProductFactory.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Yves/AnyFile.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Glue/PriceProduct/Controller/AnyFile.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Glue/PriceProduct/Dependency/AnyFile.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Glue/PriceProduct/Plugin/AnyFile.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Glue/PriceProduct/PriceProductConfig.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Glue/PriceProduct/PriceProductDependencyProvider.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Glue/PriceProduct/PriceProductFactory.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Glue/PriceProduct/PriceProductResource.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Glue/PriceProduct/PriceProductResourceInterface.php'],
        ];
    }

    /**
     * @return array<array<string>>
     */
    public function nonPublicApiFilesDataProvider(): array
    {
        return [
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Business/Model/PriceGrouper.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Business/Model/Reader.php'],
            ['/data/project/b2b-demo-shop/src/Pyz/Zed/PriceProduct/Business/CurrencyReaderInterface.php'],
        ];
    }
}
