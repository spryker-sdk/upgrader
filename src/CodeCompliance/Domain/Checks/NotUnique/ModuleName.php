<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\NotUnique;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;

class ModuleName extends AbstractCodeComplianceCheck
{
    /**
     * @var string
     */
    protected const DOCUMENTATION_URL_PATH = 'entity-name-is-not-unique.html#notunique-modulename';

    /**
     * @var array<string> $ignoreModuleNames
     *
     * The list was added for skipping issues in demo shops until they are not updated
     */
    protected array $ignoreModuleNames = [
        'ExampleProductSalePage',
        'ProductUrlCartConnector',
        'CustomerFullNameWidget',
        'ExampleChart',
        'NavigationWidget',
        'ExampleStateMachine',
        'EvaluationModule',
    ];

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:ModuleName';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Module %s has to have project prefix, like %s%s.';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];
        $coreModuleNames = $this->getCodebaseSourceDto()->getCoreModuleNames();
        $projectModuleNames = $this->getCodebaseSourceDto()->getProjectModuleNames();
        $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

        foreach ($projectModuleNames as $projectModuleName) {
            $hasProjectPrefix = $this->hasProjectPrefix($projectModuleName, $projectPrefixes);
            if (in_array($projectModuleName, $coreModuleNames) || $hasProjectPrefix) {
                continue;
            }
            if (in_array($projectModuleName, $this->ignoreModuleNames)) {
                continue;
            }

            $guideline = sprintf(
                $this->getGuideline(),
                $projectModuleName,
                reset($projectPrefixes),
                $projectModuleName,
            );
            $violations[] = new Violation((string)new Id(), $guideline, $this->getName(), ViolationInterface::SEVERITY_ERROR, [
                static::KEY_ATTRIBUTE_DOCUMENTATION => $this->getDocumentationUrl(),
            ]);
        }

        return $violations;
    }
}
