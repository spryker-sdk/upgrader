<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Console\Parser;

use Codebase\Application\Dto\ModuleDto;
use Symfony\Component\Console\Input\InputInterface;
use Upgrade\Application\Exception\UpgraderException;

class OptionModuleParser implements OptionModuleParserInterface
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
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return array<\Codebase\Application\Dto\ModuleDto>
     */
    public function getModuleList(InputInterface $input): array
    {
        $modules = [];
        $moduleOption = (string)$input->getOption(static::OPTION_MODULE);

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
