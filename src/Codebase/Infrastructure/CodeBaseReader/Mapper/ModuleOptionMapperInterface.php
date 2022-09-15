<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\CodeBaseReader\Mapper;

interface ModuleOptionMapperInterface
{
    /**
     * @param string|null $moduleOption
     *
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return array<\Codebase\Application\Dto\ModuleDto>
     */
    public function mapToModuleList(?string $moduleOption): array;
}
