<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto;

use Upgrade\Application\Dto\ViolationDtoInterface;

class ViolationDto implements ViolationDtoInterface
{
    /**
     * @var array<string>
     */
    protected array $composerCommands;

    /**
     * @var array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto>
     */
    protected array $fileErrors;

    /**
     * @var string
     */
    protected string $hash;

    /**
     * @param array<string> $composerCommands
     * @param array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto> $fileErrors
     */
    public function __construct(array $composerCommands, array $fileErrors)
    {
        $this->composerCommands = $composerCommands;
        $this->fileErrors = $fileErrors;
        $this->hash = sha1(
            implode(' ', $composerCommands) .
            implode(' ', array_map(static fn (FileErrorDto $el): string => $el->getMessage() . $el->getFilename(), $fileErrors)),
        );
    }

    /**
     * @return array<string>
     */
    public function getComposerCommands(): array
    {
        return $this->composerCommands;
    }

    /**
     * @return array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto>
     */
    public function getFileErrors(): array
    {
        return $this->fileErrors;
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
