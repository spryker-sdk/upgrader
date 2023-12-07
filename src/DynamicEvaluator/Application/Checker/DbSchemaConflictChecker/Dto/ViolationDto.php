<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\Dto;

use Upgrade\Application\Dto\ViolationDtoInterface;

class ViolationDto implements ViolationDtoInterface
{
    /**
     * @var string
     */
    protected string $projectFile;

    /**
     * @var string
     */
    protected string $table;

    /**
     * @var array<string>
     */
    protected array $columns;

    /**
     * @var string
     */
    protected string $hash;

    /**
     * @param string $projectFile
     * @param string $table
     * @param array<string> $columns
     */
    public function __construct(string $projectFile, string $table, array $columns)
    {
        $this->projectFile = $projectFile;
        $this->table = $table;
        $this->columns = $columns;
        $this->hash = sha1($projectFile . $table . implode('', $columns));
    }

    /**
     * @return string
     */
    public function getProjectFile(): string
    {
        return $this->projectFile;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return array<string>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param \Upgrade\Application\Dto\ViolationDtoInterface $violationDto
     *
     * @return bool
     */
    public function equals(ViolationDtoInterface $violationDto): bool
    {
        return $violationDto instanceof self && $violationDto->getHash() === $this->getHash();
    }
}
