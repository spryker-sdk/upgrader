<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain;

use Codebase\Application\Dto\CodebaseSourceDto;
use Resolve\Domain\Service\CodeBaseServiceInterface;
use Resolve\Domain\Service\FilterService;

abstract class AbstractResolveCheck implements ResolveInterface
{
    /**
     * @var string
     */
    protected const COLUMN_KEY_NAME = 'name';

    /**
     * @var CodebaseSourceDto
     */
    protected CodebaseSourceDto $codebaseSourceDto;

    /**
     * @var \Resolve\Domain\Service\FilterService
     */
    protected FilterService $filterService;

    /**
     * @var CodeBaseServiceInterface
     */
    protected CodeBaseServiceInterface $codeBaseService;

    /**
     * @param \Resolve\Domain\Service\FilterService $filterService
     * @param CodeBaseServiceInterface $codeBaseService
     */
    public function __construct(FilterService $filterService, CodeBaseServiceInterface $codeBaseService)
    {
        $this->filterService = $filterService;
        $this->codeBaseService = $codeBaseService;
    }

    /**
     * @param CodebaseSourceDto $codebaseSourceDto
     *
     * @return $this
     */
    public function setCodebaseSourceDto(CodebaseSourceDto $codebaseSourceDto)
    {
        $this->codebaseSourceDto = $codebaseSourceDto;

        return $this;
    }

    /**
     * @return CodebaseSourceDto
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
