<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\PublicApiFilePathsProvider;

class PublicApiFilePathsProvider implements PublicApiFilePathsProviderInterface
{
    /**
     * @var array<string>
     */
    protected const PUBLIC_API_FILES = [
        // Zed
        'src/*/Zed/*/*DependencyProvider.php',
        'src/*/Zed/*/*Config.php',
        // Zed Business
        'src/*/Zed/*/Business/*BusinessFactory.php',
        'src/*/Zed/*/Business/*Facade.php',
        'src/*/Zed/*/Business/*FacadeInterface.php',
        // Zed Communication
        'src/*/Zed/*/Communication/',
        // Zed Dependency
        'src/*/Zed/*/Dependency/',
        // Zed Persistence
        'src/*/Zed/*/Persistence/*EntityManager.php',
        'src/*/Zed/*/Persistence/*EntityManagerInterface.php',
        'src/*/Zed/*/Persistence/*QueryContainer.php',
        'src/*/Zed/*/Persistence/*QueryContainerInterface.php',
        'src/*/Zed/*/Persistence/*Repository.php',
        'src/*/Zed/*/Persistence/*RepositoryInterface.php',
        // Zed Presentation
        'src/*/Zed/*/Presentation/',
        // Shared
        'src/*/Shared/',
        // Service
        'src/*/Service/*/*Config.php',
        'src/*/Service/*/*DependencyProvider.php',
        'src/*/Service/*/*Service.php',
        'src/*/Service/*/*ServiceInterface.php',
        'src/*/Service/*/*ServiceFactory.php',
        // Client
        'src/*/Client/*/*Client.php',
        'src/*/Client/*/*ClientInterface.php',
        'src/*/Client/*/*Config.php',
        'src/*/Client/*/*DependencyProvider.php',
        'src/*/Client/*/*Factory.php',
        // Yves
        'src/*/Yves/',
        // Glue
        'src/*/Glue/*/*Config.php',
        'src/*/Glue/*/*DependencyProvider.php',
        'src/*/Glue/*/*Factory.php',
        'src/*/Glue/*/*Resource.php',
        'src/*/Glue/*/*ResourceInterface.php',
        'src/*/Glue/*/Controller/',
        'src/*/Glue/*/Dependency/',
        'src/*/Glue/*/Plugin/',

    ];

    /**
     * @return array<string>
     */
    public function getPublicApiFilePathsRegexCollection(): array
    {
        return array_map(
            static fn (string $el): string => strtr($el, ['.' => '\.', '*' => '\w*']),
            static::PUBLIC_API_FILES,
        );
    }
}
