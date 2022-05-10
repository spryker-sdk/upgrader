<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\Cache\File;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Infrastructure\SourceParser\Cache\SourceCacheInterface;
use JMS\Serializer\SerializerBuilder;

class FileSourceCache implements SourceCacheInterface
{
    /**
     * @var string
     */
    protected const CACHE_TYPE = 'file';

    /**
     * @var string
     */
    protected const FILE_LOCATION_PATTERN = 'data/cache/%s_%s.cache';

    /**
     * @var \JMS\Serializer\SerializerBuilder
     */
    protected $serializer;

    /**
     * @param \JMS\Serializer\SerializerBuilder $serializer
     */
    public function __construct(SerializerBuilder $serializer)
    {
        $this->serializer = $serializer->build();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return static::CACHE_TYPE;
    }

    /**
     * @param string $id
     * @param string $extension
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $codebaseSources
     *
     * @return void
     */
    public function writeCache(string $id, string $extension, array $codebaseSources): void
    {
        $filePath = $this->getFilePath($id, $extension);

        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        file_put_contents($filePath, $this->serializer->serialize($codebaseSources, 'json'));
    }

    /**
     * @param string $id
     * @param string $extension
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function readCache(string $id, string $extension): array
    {
        $filePath = $this->getFilePath($id, $extension);

        if (!file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);

        $deserializedContent = $this->serializer->deserialize($content, sprintf('array<%s>', ClassCodebaseDto::class), 'json');

        return $deserializedContent;
    }

    /**
     * @param string $id
     * @param string $extension
     *
     * @return string
     */
    protected function getFilePath(string $id, string $extension): string
    {
        return sprintf(static::FILE_LOCATION_PATTERN, $id, $extension);
    }
}
