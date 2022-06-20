<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\Filters\FacadeFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

class Facade extends AbstractUsedCodeComplianceCheck
{
    /**
     * @var string
     */
    protected const DOC_COMMENT_MESSAGE = 'Please add doc comment for %s';

    /**
     * @var array<string>
     */
    protected const USED_PRIVATE_API_ANNOTATION = ['getRepository', 'getEntityManager', 'getFactory'];

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:Facade';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Please avoid usage of %s(...) in %s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $codebaseSourceDto = $this->getCodebaseSourceDto();
        $filteredSources = $this->filterService->filter($codebaseSourceDto->getPhpCodebaseSources(), [
            FacadeFilter::FACADE_FILTER,
        ]);
        $violations = [];

        foreach (static::USED_PRIVATE_API_ANNOTATION as $privateApiAnnotation) {
            foreach ($filteredSources as $source) {
                $classFileBody = $this->getClassFileBody($source->getReflection());
                if (!$classFileBody) {
                    continue;
                }

                $usedMethodNames = $this->parseUsedMethodsFromGetter($classFileBody, $privateApiAnnotation);
                if (!$usedMethodNames) {
                    continue;
                }

                $classDocComment = $source->getReflection()->getDocComment();
                if (!$classDocComment) {
                    $message = sprintf(static::DOC_COMMENT_MESSAGE, $source->getReflection()->getName());
                    $violations[] = new Violation(new Id(), $message, $this->getName());

                    continue;
                }

                $namespace = $this->getReturnNamespaceByMethodFromDocComment(
                    $classDocComment,
                    $privateApiAnnotation,
                );

                if (!$namespace) {
                    $message = sprintf(static::DOC_COMMENT_MESSAGE, '$this->' . $privateApiAnnotation . '() in' . $source->getClassName());
                    $violations[] = new Violation(new Id(), $message, $this->getName());

                    continue;
                }

                $codebaseDto = $this->codeBaseAdapter->parsePhpClass(
                    $namespace,
                    $codebaseSourceDto->getProjectPrefixes(),
                    $codebaseSourceDto->getCoreNamespaces(),
                );
                if (!$codebaseDto) {
                    continue;
                }

                foreach ($usedMethodNames as $usedMethodName) {
                    $methodReflection = $codebaseDto->getReflection()->getMethod($usedMethodName);
                    $hasCoreNamespace = $this->hasCoreNamespace(
                        $codebaseSourceDto->getCoreNamespaces(),
                        $methodReflection->getDeclaringClass()->getName(),
                    );
                    if ($hasCoreNamespace) {
                        $guideline = sprintf($this->getGuideline(), $usedMethodName, $source->getClassName());
                        $violations[] = new Violation(new Id(), $guideline, $this->getName());
                    }
                }
            }
        }

        return $violations;
    }
}
