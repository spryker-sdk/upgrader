<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Configuration\ConfigurationProvider;
use CodeCompliance\Domain\Service\CodeBaseServiceInterface;
use CodeCompliance\Domain\Service\FilterService;

abstract class AbstractCodeComplianceCheck implements CodeComplianceCheckInterface
{
    /**
     * @var string
     */
    protected const KEY_ATTRIBUTE_DOCUMENTATION = 'documentation';

    /**
     * @var string
     */
    protected const COLUMN_KEY_NAME = 'name';

    /**
     * @var string
     */
    protected const DOCUMENTATION_URL_PATH = '';

    /**
     * @var \Codebase\Application\Dto\CodebaseSourceDto
     */
    protected CodebaseSourceDto $codebaseSourceDto;

    /**
     * @var \CodeCompliance\Domain\Service\FilterService
     */
    protected FilterService $filterService;

    /**
     * @var \CodeCompliance\Domain\Service\CodeBaseServiceInterface
     */
    protected CodeBaseServiceInterface $codeBaseService;

    /**
     * @var \CodeCompliance\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configProvider;

    /**
     * @param \CodeCompliance\Domain\Service\FilterService $filterService
     * @param \CodeCompliance\Domain\Service\CodeBaseServiceInterface $codeBaseService
     * @param \CodeCompliance\Configuration\ConfigurationProvider $configProvider
     */
    public function __construct(FilterService $filterService, CodeBaseServiceInterface $codeBaseService, ConfigurationProvider $configProvider)
    {
        $this->filterService = $filterService;
        $this->codeBaseService = $codeBaseService;
        $this->configProvider = $configProvider;
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
     * @return string
     */
    public function getDocumentationUrl(): string
    {
        return $this->configProvider->getDocumentationBaseUrl() . static::DOCUMENTATION_URL_PATH;
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
