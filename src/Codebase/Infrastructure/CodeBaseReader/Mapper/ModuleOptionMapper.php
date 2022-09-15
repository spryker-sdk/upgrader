<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\CodeBaseReader\Mapper;

use Codebase\Application\Dto\ModuleDto;
use Upgrade\Application\Exception\UpgraderException;

class ModuleOptionMapper implements ModuleOptionMapperInterface
{
    /**
     * @var int
     */
    protected const MODULE_NAMESPACE_INDEX = 0;

    /**
     * @var int
     */
    protected const MODULE_NAME_INDEX = 1;

    /**
     * @var string
     */
    public const MODULE_SEPARATOR = ' ';

    /**
     * @var string
     */
    public const NAMESPACE_NAME_SEPARATOR = '.';

    /**
     * @param string|null $moduleOption
     *
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return array<\Codebase\Application\Dto\ModuleDto>
     */
    public function mapToModuleList(?string $moduleOption): array
    {
        $modules = [];

        if (!$moduleOption) {
            return $modules;
        }

        foreach (explode(self::MODULE_SEPARATOR, $moduleOption) as $namespaceName) {
            $moduleData = explode(self::NAMESPACE_NAME_SEPARATOR, $namespaceName);

            if (!isset($moduleData[self::MODULE_NAME_INDEX])) {
                throw new UpgraderException('Please specify module with namespace {Namespace}.{ModuleName}. Example: Pyz.DataImport');
            }

            $modules[] = new ModuleDto($moduleData[self::MODULE_NAMESPACE_INDEX], $moduleData[self::MODULE_NAME_INDEX]);
        }

        return $modules;
    }
}
