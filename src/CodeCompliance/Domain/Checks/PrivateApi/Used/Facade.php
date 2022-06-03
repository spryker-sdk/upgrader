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
        $codebaseSources = $this->getCodebaseSourceDto()->getPhpCodebaseSources();
        $filteredSources = $this->filterService->filter($codebaseSources, [
            FacadeFilter::FACADE_FILTER,
        ]);
        $violations = [];

        foreach (static::USED_PRIVATE_API_ANNOTATION as $privateApiAnnotation) {
            /** @var \Codebase\Application\Dto\ClassCodebaseDto $source */
            foreach ($filteredSources as $source) {
                $classFileBody = $this->getClassFileBody($source);
                if (!$classFileBody) {
                    continue;
                }

                $usedMethodNames = $this->parseUsedMethodsFromGetter($classFileBody, $privateApiAnnotation);
                if (!$usedMethodNames) {
                    continue;
                }

                $classDocComment = $source->getDocComment();
                if (!$classDocComment) {
                    $message = sprintf(static::DOC_COMMENT_MESSAGE, $source->getName());
                    $violations[] = new Violation(new Id(), $message, $this->getName());

                    continue;
                }

                $namespace = $this->getReturnNamespaceByMethodFromDocComment(
                    $classDocComment,
                    $privateApiAnnotation,
                );

                if (!$namespace) {
                    $message = sprintf(static::DOC_COMMENT_MESSAGE, '$this->' . $privateApiAnnotation . '() in' . $source->getName());
                    $violations[] = new Violation(new Id(), $message, $this->getName());

                    continue;
                }

                $codebaseCoreSources = $this->getCodebaseSourceDto()->getPhpCoreCodebaseSources();
                /** @var \Codebase\Application\Dto\ClassCodebaseDto $codebaseDto */
                $codebaseDto = $codebaseSources[$namespace] ?? $codebaseCoreSources[$namespace] ?? null;
                if (!$codebaseDto) {
                    continue;
                }

                foreach ($usedMethodNames as $usedMethodName) {
                    $methodReflection = $codebaseDto->getMethod($usedMethodName);
                    $hasCoreNamespace = $this->hasCoreNamespace(
                        $this->getCodebaseSourceDto()->getCoreNamespaces(),
                        $methodReflection->getDeclaringClass()->getName(),
                    );
                    if ($hasCoreNamespace) {
                        $guideline = sprintf($this->getGuideline(), $usedMethodName, $source->getName());
                        $violations[] = new Violation(new Id(), $guideline, $this->getName());
                    }
                }
            }
        }

        return $violations;
    }
}
