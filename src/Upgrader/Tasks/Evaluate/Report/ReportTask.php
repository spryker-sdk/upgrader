<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader\Tasks\Evaluate\Report;

use SprykerSdk\SdkContracts\Entity\Lifecycle\LifecycleInterface;
use SprykerSdk\SdkContracts\Entity\TaskInterface;
use Upgrader\Commands\Evaluate\Report\ReportCommand;
use Upgrader\Lifecycle\Lifecycle;

class ReportTask implements TaskInterface
{
    /**
     * @var string
     */
    public const ID_REPORT_TASK = 'analyze:php:code-compliance-report';

    /**
     * @return string
     */
    public function getId(): string
    {
        return static::ID_REPORT_TASK;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return 'Report code compliance issues.';
    }

    /**
     * @return array<\SprykerSdk\SdkContracts\Entity\CommandInterface>
     */
    public function getCommands(): array
    {
        return [
            new ReportCommand(),
        ];
    }

    /**
     * @return array<\SprykerSdk\SdkContracts\Entity\PlaceholderInterface>
     */
    public function getPlaceholders(): array
    {
        return [];
    }

    /**
     * @return string|null
     */
    public function getHelp(): ?string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return '0.1.0';
    }

    /**
     * @return bool
     */
    public function isDeprecated(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getSuccessor(): ?string
    {
        return null;
    }

    /**
     * @return \SprykerSdk\SdkContracts\Entity\Lifecycle\LifecycleInterface
     */
    public function getLifecycle(): LifecycleInterface
    {
        return new Lifecycle();
    }

    /**
     * @return array<string>
     */
    public function getStages(): array
    {
        return [];
    }
}
