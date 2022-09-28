<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Application\Dto;

class CodebaseSourceDto
{
    /**
     * @var array<string>
     */
    protected array $coreNamespaces = [];

    /**
     * @var array<\Codebase\Application\Dto\CodebaseInterface>
     */
    protected array $phpCodebaseSources = [];

    /**
     * @var array<string>
     */
    protected array $coreModuleNames = [];

    /**
     * @var array<string>
     */
    protected array $projectModuleNames = [];

    /**
     * @var array<\Codebase\Application\Dto\CodebaseInterface>
     */
    protected array $xmlDatabaseSchemaCodebaseSources = [];

    /**
     * @var array<\Codebase\Application\Dto\CodebaseInterface>
     */
    protected array $xmlTransferSchemaCodebaseSources = [];

    /**
     * @var array<\Codebase\Application\Dto\CodebaseInterface>
     */
    protected array $xmlDatabaseSchemaCoreCodebaseSources = [];

    /**
     * @var array<\Codebase\Application\Dto\CodebaseInterface>
     */
    protected array $xmlTransferSchemaCoreCodebaseSources = [];

    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var array<string>
     */
    protected array $projectPrefixes = [];

    /**
     * @param array<string> $coreNamespaces
     * @param array<string> $projectPrefixes
     */
    public function __construct(array $coreNamespaces, array $projectPrefixes)
    {
        $this->coreNamespaces = $coreNamespaces;
        $this->projectPrefixes = $projectPrefixes;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $codebaseSources
     * @param string|null $type
     *
     * @return $this
     */
    public function setPhpCodebaseSources(array $codebaseSources, ?string $type = null)
    {
        if ($type === null || $type === SourceParserRequestDto::PROJECT_TYPE) {
            $this->phpCodebaseSources = array_merge($this->phpCodebaseSources, $codebaseSources);

            return $this;
        }

        return $this;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $codebaseSources
     * @param string|null $type
     *
     * @return $this
     */
    public function setDatabaseSchemaCodebaseSources(array $codebaseSources, ?string $type = null)
    {
        if ($type === null || $type === SourceParserRequestDto::PROJECT_TYPE) {
            $this->xmlDatabaseSchemaCodebaseSources = array_merge($this->xmlDatabaseSchemaCodebaseSources, $codebaseSources);

            return $this;
        }

        $this->xmlDatabaseSchemaCoreCodebaseSources = array_merge($this->xmlDatabaseSchemaCoreCodebaseSources, $codebaseSources);

        return $this;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $codebaseSources
     * @param string|null $type
     *
     * @return $this
     */
    public function setTransferSchemaCodebaseSources(array $codebaseSources, ?string $type = null)
    {
        if ($type === null || $type === SourceParserRequestDto::PROJECT_TYPE) {
            $this->xmlTransferSchemaCodebaseSources = array_merge($this->xmlTransferSchemaCodebaseSources, $codebaseSources);

            return $this;
        }

        $this->xmlTransferSchemaCoreCodebaseSources = array_merge($this->xmlTransferSchemaCoreCodebaseSources, $codebaseSources);

        return $this;
    }

    /**
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function getPhpCodebaseSources(): array
    {
        return $this->phpCodebaseSources;
    }

    /**
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function getDatabaseSchemaCodebaseSources(): array
    {
        return $this->xmlDatabaseSchemaCodebaseSources;
    }

    /**
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function getDatabaseSchemaCoreCodebaseSources(): array
    {
        return $this->xmlDatabaseSchemaCoreCodebaseSources;
    }

    /**
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function getTransferSchemaCodebaseSources(): array
    {
        return $this->xmlTransferSchemaCodebaseSources;
    }

    /**
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function getTransferSchemaCoreCodebaseSources(): array
    {
        return $this->xmlTransferSchemaCoreCodebaseSources;
    }

    /**
     * @return array<string>
     */
    public function getCoreNamespaces(): array
    {
        return $this->coreNamespaces;
    }

    /**
     * @return array<string>
     */
    public function getProjectPrefixes(): array
    {
        return $this->projectPrefixes;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string>
     */
    public function getCoreModuleNames(): array
    {
        return $this->coreModuleNames;
    }

    /**
     * @param array<string> $coreModuleNames
     *
     * @return void
     */
    public function setCoreModuleNames(array $coreModuleNames): void
    {
        $this->coreModuleNames = $coreModuleNames;
    }

    /**
     * @return array<string>
     */
    public function getProjectModuleNames(): array
    {
        return $this->projectModuleNames;
    }

    /**
     * @param array<string> $projectModuleNames
     *
     * @return void
     */
    public function setProjectModuleNames(array $projectModuleNames): void
    {
        $this->projectModuleNames = $projectModuleNames;
    }
}
