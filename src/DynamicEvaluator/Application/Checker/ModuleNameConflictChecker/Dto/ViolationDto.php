<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto;

use Upgrade\Application\Dto\ViolationDtoInterface;

class ViolationDto implements ViolationDtoInterface
{
    /**
     * @var array<string>
     */
    protected array $existingModules;

    /**
     * @var array<string>
     */
    protected array $composerCommands;

    /**
     * @var string
     */
    protected string $hash;

    /**
     * @param array<string> $existingModules
     * @param array<string> $composerCommands
     */
    public function __construct(array $existingModules, array $composerCommands)
    {
        sort($existingModules);

        $this->existingModules = $existingModules;
        $this->composerCommands = $composerCommands;
        $this->hash = implode(' ', $existingModules) . implode(' ', $composerCommands);
    }

    /**
     * @return array<string>
     */
    public function getExistingModules(): array
    {
        return $this->existingModules;
    }

    /**
     * @return array<string>
     */
    public function getComposerCommands(): array
    {
        return $this->composerCommands;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param \Upgrade\Application\Dto\ViolationDtoInterface $violationDto
     *
     * @return bool
     */
    public function equals(ViolationDtoInterface $violationDto): bool
    {
        return $violationDto instanceof ViolationDto && $this->getHash() === $violationDto->getHash();
    }
}
