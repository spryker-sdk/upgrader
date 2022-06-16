<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Domain\Adapter\CodeBaseReaderInterface;
use CodeCompliance\Domain\Service\FilterService;

abstract class AbstractCodeComplianceCheck implements CodeComplianceCheckInterface
{
    /**
     * @var string
     */
    protected const COLUMN_KEY_NAME = 'name';

    /**
     * @var \Codebase\Application\Dto\CodebaseSourceDto
     */
    protected CodebaseSourceDto $codebaseSourceDto;

    /**
     * @var \CodeCompliance\Domain\Service\FilterService
     */
    protected FilterService $filterService;

    /**
     * @var \CodeCompliance\Domain\Adapter\CodeBaseReaderInterface
     */
    protected CodeBaseReaderInterface $codeBaseAdapter;

    /**
     * @param \CodeCompliance\Domain\Service\FilterService $filterService
     * @param \CodeCompliance\Domain\Adapter\CodeBaseReaderInterface $codeBaseAdapter
     */
    public function __construct(FilterService $filterService, CodeBaseReaderInterface $codeBaseAdapter)
    {
        $this->filterService = $filterService;
        $this->codeBaseAdapter = $codeBaseAdapter;
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return $this
     */
    public function setCodebaseSourceDto(CodebaseSourceDto $codebaseSourceDto)
    {
        $this->codebaseSourceDto = $codebaseSourceDto;

        return $this;
    }

    /**
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function getCodebaseSourceDto(): CodebaseSourceDto
    {
        return $this->codebaseSourceDto;
    }

    /**
     * @param array<string> $coreNamespaces
     * @param string $namespace
     *
     * @return bool
     */
    public function hasCoreNamespace(array $coreNamespaces, string $namespace): bool
    {
        foreach ($coreNamespaces as $coreNamespace) {
            if (strpos($namespace, $coreNamespace) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $docComment
     *
     * @return bool
     */
    protected function isPluginReturnInDocComment(string $docComment): bool
    {
        return (bool)preg_match('/.*@return.*PluginInterface.*/m', $docComment);
    }

    /**
     * @param string $value
     * @param array<string> $projectPrefixes
     *
     * @return bool
     */
    protected function hasProjectPrefix(string $value, array $projectPrefixes): bool
    {
        foreach ($projectPrefixes as $projectPrefix) {
            if (stripos($value, $projectPrefix) === 0) {
                return true;
            }
        }

        return false;
    }
}
