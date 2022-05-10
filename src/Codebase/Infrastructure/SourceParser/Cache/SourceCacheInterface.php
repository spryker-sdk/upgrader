<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\Cache;

interface SourceCacheInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $id
     * @param string $extension
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $codebaseSources
     *
     * @return void
     */
    public function writeCache(string $id, string $extension, array $codebaseSources): void;

    /**
     * @param string $id
     * @param string $extension
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function readCache(string $id, string $extension): array;
}
