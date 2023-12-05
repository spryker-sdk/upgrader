<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser;

use InvalidArgumentException;
use SimpleXMLElement;
use SprykerSdk\Utils\Infrastructure\Service\Filesystem;

class XmlSchemaFileParser implements XmlSchemaFileParserInterface
{
    /**
     * @var string
     */
    protected const TABLE_NODE_NAME = 'table';

    /**
     * @var string
     */
    protected const COLUMN_NODE_NAME = 'column';

    /**
     * @var string
     */
    protected const NAME_ATTRIBUTE = 'name';

    /**
     * @var \SprykerSdk\Utils\Infrastructure\Service\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \SprykerSdk\Utils\Infrastructure\Service\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $schemaFile
     *
     * @throws \InvalidArgumentException
     *
     * @return array<string, array<string>>
     */
    public function parseXmlToColumnsMap(string $schemaFile): array
    {
        if (!$this->filesystem->exists($schemaFile)) {
            return [];
        }

        $fileContent = $this->filesystem->readFile($schemaFile);

        $rootNode = simplexml_load_string($fileContent);

        if ($rootNode === false) {
            throw new InvalidArgumentException(sprintf('Unable to parse xml %s', $schemaFile));
        }

        return $this->parseSchemaXmlToColumnsMap($rootNode, $schemaFile);
    }

    /**
     * @param \SimpleXMLElement $rootNode
     * @param string $schemaFile
     *
     * @return array<string, array<string>>
     */
    protected function parseSchemaXmlToColumnsMap(SimpleXMLElement $rootNode, string $schemaFile): array
    {
        $nodes = [];

        /** @var \SimpleXMLElement $xmlNodeChildren */
        $xmlNodeChildren = $rootNode->children();

        foreach ($xmlNodeChildren as $table) {
            if ($table->getName() !== static::TABLE_NODE_NAME) {
                continue;
            }

            $tableName = $this->getNameAttribute($table, $schemaFile);

            foreach ($table->children() as $column) {
                if ($column->getName() !== static::COLUMN_NODE_NAME) {
                    continue;
                }

                $columnName = $this->getNameAttribute($column, $schemaFile);

                $nodes[$tableName][] = $columnName;
            }
        }

        return $nodes;
    }

    /**
     * @param \SimpleXMLElement $element
     * @param string $filePath
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function getNameAttribute(SimpleXMLElement $element, string $filePath): string
    {
        /** @var \SimpleXMLElement $xmlAttributes */
        $xmlAttributes = $element->attributes();

        foreach ($xmlAttributes as $attribute => $value) {
            if ($attribute === static::NAME_ATTRIBUTE) {
                return (string)$value;
            }
        }

        throw new InvalidArgumentException(sprintf('Required attribute "name" is not found in %s', $filePath));
    }
}
