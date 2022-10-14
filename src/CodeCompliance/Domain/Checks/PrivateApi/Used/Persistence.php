<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\Filters\PersistenceFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;

class Persistence extends AbstractUsedCodeComplianceCheck
{
    /**
     * @var string
     */
    protected const DOCUMENTATION_URL_PATH = 'private-api-is-used-on-the-project-level.html#privateapi-persistence';

    /**
     * @var string
     */
    protected const DOC_COMMENT_MESSAGE = 'Please add doc comment for %s';

    /**
     * @var array<string>
     */
    protected const METHODS_TO_CHECK = ['getFactory'];

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:Persistence';
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
        $sources = $this->filterService->filter($codebaseSourceDto->getPhpCodebaseSources(), [PersistenceFilter::PERSISTENCE_FILTER]);
        $violations = [];

        foreach (static::METHODS_TO_CHECK as $methodToCheck) {
            foreach ($sources as $source) {
                $classFileBody = $this->getClassFileBody($source->getReflection());
                if (!$classFileBody) {
                    continue;
                }

                $usedMethodNames = $this->parseUsedMethodsFromGetter($classFileBody, $methodToCheck);
                if ($usedMethodNames === []) {
                    continue;
                }

                $classDocComment = $source->getReflection()->getDocComment();
                if (!$classDocComment) {
                    $message = sprintf(static::DOC_COMMENT_MESSAGE, $source->getReflection()->getName());
                    $violations[] = new Violation((string)(new Id()), $message, $this->getName(), ViolationInterface::SEVERITY_ERROR, [
                        static::KEY_ATTRIBUTE_DOCUMENTATION => $this->getDocumentationUrl(),
                    ]);

                    continue;
                }

                $factoryNamespace = $this->getReturnNamespaceByMethodFromDocComment($classDocComment, $methodToCheck);
                if (!$factoryNamespace) {
                    $message = sprintf(static::DOC_COMMENT_MESSAGE, '$this->getFactory() in ' . $source->getClassName());
                    $violations[] = new Violation((string)(new Id()), $message, $this->getName(), ViolationInterface::SEVERITY_ERROR, [
                        static::KEY_ATTRIBUTE_DOCUMENTATION => $this->getDocumentationUrl(),
                    ]);

                    continue;
                }

                $classTransfer = $this->codeBaseService->parsePhpClass(
                    $factoryNamespace,
                    $codebaseSourceDto->getProjectPrefixes(),
                    $codebaseSourceDto->getCoreNamespaces(),
                );
                if ($classTransfer === null) {
                    continue;
                }

                foreach ($usedMethodNames as $usedMethodName) {
                    $methodReflection = $classTransfer->getReflection()->getMethod($usedMethodName);
                    $hasCoreNamespace = $this->hasCoreNamespace($codebaseSourceDto->getCoreNamespaces(), $methodReflection->getDeclaringClass()->getName());

                    if ($hasCoreNamespace) {
                        $guideline = sprintf($this->getGuideline(), $usedMethodName, $source->getClassName());
                        $violations[] = new Violation((string)(new Id()), $guideline, $this->getName(), ViolationInterface::SEVERITY_ERROR, [
                            static::KEY_ATTRIBUTE_DOCUMENTATION => $this->getDocumentationUrl(),
                        ]);
                    }
                }
            }
        }

        return $violations;
    }
}
