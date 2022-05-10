<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\Cache;

use Codebase\Infrastructure\Exception\SourceCacheTypeNotExistException;
use Core\Infrastructure\Services\ComposerLockReader;

class SourceCache
{
    /**
     * @var ?string
     */
    protected static ?string $cache_identifier = null;

    /**
     * @var string
     */
    protected const COMPOSER_LOCK_CONTENT_HASH_KEY = 'content-hash';

    /**
     * @var array<\Codebase\Infrastructure\SourceParser\Cache\SourceCacheInterface>
     */
    protected array $sourceCacheTypes = [];

    /**
     * @var \Core\Infrastructure\Services\ComposerLockReader
     */
    protected ComposerLockReader $composerLockReader;

    /**
     * @param \Core\Infrastructure\Services\ComposerLockReader $composerLockReader
     * @param array<\Codebase\Infrastructure\SourceParser\Cache\SourceCacheInterface> $sourceCacheTypes
     */
    public function __construct(ComposerLockReader $composerLockReader, array $sourceCacheTypes = [])
    {
        $this->composerLockReader = $composerLockReader;
        $this->sourceCacheTypes = $sourceCacheTypes;
    }

    /**
     * @param string $type
     *
     * @throws \Codebase\Infrastructure\Exception\SourceCacheTypeNotExistException
     *
     * @return \Codebase\Infrastructure\SourceParser\Cache\SourceCacheInterface
     */
    public function getSourceCacheType(string $type = 'file'): SourceCacheInterface
    {
        foreach ($this->sourceCacheTypes as $sourceCacheType) {
            if ($sourceCacheType->getType() === $type) {
                return $sourceCacheType;
            }
        }

        throw new SourceCacheTypeNotExistException();
    }

    /**
     * @return string
     */
    public function getCacheIdentifier(): string
    {
        if (static::$cache_identifier == null) {
            $contentHash = $this->composerLockReader->readComposerLockDataByKey(static::COMPOSER_LOCK_CONTENT_HASH_KEY);
            static::$cache_identifier = $contentHash;
        }

        return static::$cache_identifier;
    }
}
