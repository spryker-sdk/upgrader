<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\PrivateApi\Extended;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Checks\Filters\BusinessModelFilter;
use CodeCompliance\Domain\Checks\Filters\CoreExtensionFilter;
use CodeCompliance\Domain\Checks\Filters\IgnoreListParentFilter;
use CodeCompliance\Domain\Checks\Filters\PluginFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;

class Extended extends AbstractCodeComplianceCheck
{
    /**
     * @var string
     */
    protected const DOCUMENTATION_URL_PATH = 'private-api-is-used-on-the-project-level.html#privateapi-extension';

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:Extension';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Please avoid extension of the PrivateApi %s in %s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $sources = $this->getCodebaseSourceDto()->getPhpCodebaseSources();
        $filteredSources = $this->filterService->filter($sources, [
            BusinessModelFilter::BUSINESS_MODEL_FILTER,
            CoreExtensionFilter::CORE_EXTENSION_FILTER,
            IgnoreListParentFilter::IGNORE_LIST_PARENT_FILTER,
        ]);

        $violations = [];

        foreach ($filteredSources as $source) {
            $coreParent = $this->filterService->filter([$source->getCoreParent()], [
                PluginFilter::PLUGIN_FILTER,
            ]);
            $coreParent = array_shift($coreParent);
            if (!$coreParent) {
                continue;
            }

            $guideline = sprintf($this->getGuideline(), $coreParent->getClassName(), $source->getClassName());
            $violations[] = new Violation((string)(new Id()), $guideline, $this->getName(), ViolationInterface::SEVERITY_ERROR, [
                static::KEY_ATTRIBUTE_DOCUMENTATION => $this->getDocumentationUrl(),
            ]);
        }

        return $violations;
    }
}
