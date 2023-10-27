<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Validator;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Exception\UpgraderException;
use Upgrade\Application\Validator\ProjectValidator;
use Upgrade\Application\Validator\ProjectValidatorRuleInterface;

class ProjectValidatorTest extends TestCase
{
    /**
     * @return void
     */
    public function testValidateProjectShouldInvokeValidatorRules(): void
    {
        // Arrange
        $errorMessage = 'error message';
        $errorTitle = 'error title';

        $projectValidatorRuleMock = $this->createProjectValidatorRuleMock($errorMessage, $errorTitle);
        $projectValidator = new ProjectValidator([$projectValidatorRuleMock]);

        // Act
        $violations = $projectValidator->validateProject();

        // Assert
        $this->assertCount(1, $violations);
        $this->assertSame($errorMessage, $violations[0]->getMessage());
        $this->assertSame($errorTitle, $violations[0]->getTitle());
    }

    /**
     * @param string $message
     * @param string $title
     *
     * @return \Upgrade\Application\Validator\ProjectValidatorRuleInterface
     */
    protected function createProjectValidatorRuleMock(string $message, string $title): ProjectValidatorRuleInterface
    {
        $projectValidatorRule = $this->createMock(ProjectValidatorRuleInterface::class);
        $projectValidatorRule->method('validate')->willThrowException(new UpgraderException($message));
        $projectValidatorRule->method('getViolationTitle')->willReturn($title);

        return $projectValidatorRule;
    }
}
