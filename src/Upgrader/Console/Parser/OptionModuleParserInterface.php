<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Console\Parser;

use Symfony\Component\Console\Input\InputInterface;

interface OptionModuleParserInterface
{
    /**
     * @var string
     */
    public const OPTION_MODULE = 'module';

    /**
     * @var string
     */
    public const OPTION_MODULES_SHORT = '-m';

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return array<\Codebase\Application\Dto\ModuleDto>
     */
    public function getModuleList(InputInterface $input): array;
}
