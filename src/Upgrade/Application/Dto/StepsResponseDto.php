<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Domain\ValueObject\ErrorInterface;

/**
 * @codeCoverageIgnore
 */
class StepsResponseDto extends ResponseDto
{
    /**
     * @var int
     */
    public const UNDEFINED_RELEASE_GROUP_ID = 0;

    protected bool $isSuccessful;

    /**
     * @var array<string>
     */
    protected array $outputMessageList = [];

    /**
     * @var array<\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto>
     */
    protected array $appliedReleaseGroups = [];

    protected ?ComposerLockDiffDto $composerLockDiffDto = null;

    /**
     * @var array<int, \Upgrade\Application\Dto\IntegratorResponseDto>
     */
    protected array $integratorResponseCollection = [];

    protected ?ErrorInterface $error = null;

    /**
     * @var array<int, array<\Upgrade\Application\Dto\ValidatorViolationDto>>
     */
    protected array $blockers = [];

    /**
     * @var array<\Upgrade\Application\Dto\ValidatorViolationDto>
     */
    protected array $projectViolations = [];

    protected ?int $pullRequestId = null;

    protected ?string $reportId = null;

    protected bool $isStopPropagation = false;

    /**
     * @var array<int, array<\Upgrade\Application\Dto\ViolationDtoInterface>>
     */
    protected array $violations = [];

    /**
     * @var array<\Upgrade\Application\Dto\ReleaseGroupFilterResponseDto>
     */
    protected array $filterResponseList = [];

    protected ReleaseGroupStatDto $releaseGroupStatDto;

    protected ModelStatisticDto $modelStatisticDto;

    protected ?ReleaseGroupDto $currentReleaseGroup = null;

    protected bool $isPullRequestSent = false;

    protected string $targetBranch = '';

    public function __construct(bool $isSuccessful = true, ?string $outputMessage = null)
    {
        parent::__construct($isSuccessful);
        if ($outputMessage) {
            $this->outputMessageList[] = $outputMessage;
        }

        $this->releaseGroupStatDto = new ReleaseGroupStatDto();
        $this->modelStatisticDto = new ModelStatisticDto();
    }

    public function getIsSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @return $this
     */
    public function setIsSuccessful(bool $isSuccessful)
    {
        $this->isSuccessful = $isSuccessful;

        return $this;
    }

    public function getError(): ?ErrorInterface
    {
        return $this->error;
    }

    /**
     * @return $this
     */
    public function setError(?ErrorInterface $error)
    {
        $this->error = $error;

        if ($error !== null) {
            $this->addOutputMessage($error->getErrorMessage());
        }

        return $this;
    }

    public function getOutputMessage(): ?string
    {
        if (!$this->outputMessageList) {
            return null;
        }

        return implode(PHP_EOL, $this->outputMessageList);
    }

    /**
     * @return array<string>
     */
    public function getOutputMessages(): array
    {
        return $this->outputMessageList;
    }

    /**
     * @param array<string> $outputMessageList
     *
     * @return $this
     */
    public function setOutputMessages(array $outputMessageList)
    {
        $this->outputMessageList = $outputMessageList;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOutputMessage(?string $outputMessage)
    {
        if ($outputMessage) {
            $this->outputMessageList[] = $outputMessage;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setComposerLockDiff(?ComposerLockDiffDto $composerLockDiffDto)
    {
        $this->composerLockDiffDto = $composerLockDiffDto;

        return $this;
    }

    public function getComposerLockDiff(): ?ComposerLockDiffDto
    {
        return $this->composerLockDiffDto;
    }

    /**
     * @return array<int, \Upgrade\Application\Dto\IntegratorResponseDto>
     */
    public function getIntegratorResponseCollection(): array
    {
        return $this->integratorResponseCollection;
    }

    public function getIntegratorResponseDtoByReleaseGroupId(int $releaseGroupId): ?IntegratorResponseDto
    {
        return $this->integratorResponseCollection[$releaseGroupId] ?? null;
    }

    public function addIntegratorResponseDto(IntegratorResponseDto $integratorResponseDto): void
    {
        $this->integratorResponseCollection[$this->getCurrentReleaseGroupId()] = $integratorResponseDto;
    }

    public function getPullRequestId(): ?int
    {
        return $this->pullRequestId;
    }

    /**
     * @return $this
     */
    public function setPullRequestId(?int $pullRequestId)
    {
        $this->pullRequestId = $pullRequestId;

        return $this;
    }

    public function addBlocker(ValidatorViolationDto $blockerInfo): void
    {
        $currentReleaseGroupId = $this->getCurrentReleaseGroupId();

        if (!isset($this->blockers[$currentReleaseGroupId])) {
            $this->blockers[$currentReleaseGroupId] = [];
        }

        $this->blockers[$currentReleaseGroupId][] = $blockerInfo;
    }

    public function removeBlockersByTitle(string $title): void
    {
        $currentReleaseGroupId = $this->getCurrentReleaseGroupId();

        if (!isset($this->blockers[$currentReleaseGroupId])) {
            return;
        }

        $this->blockers[$currentReleaseGroupId] = array_filter(
            $this->blockers[$currentReleaseGroupId],
            static fn (ValidatorViolationDto $violation): bool => $violation->getTitle() !== $title,
        );
    }

    /**
     * @return array<int, array<\Upgrade\Application\Dto\ValidatorViolationDto>>
     */
    public function getBlockers(): array
    {
        return $this->blockers;
    }

    public function hasBlockers(): bool
    {
        return count($this->blockers) > 0;
    }

    /**
     * @return array<\Upgrade\Application\Dto\ValidatorViolationDto>
     */
    public function getBlockersByReleaseGroupId(int $releaseGroupId): array
    {
        return $this->blockers[$releaseGroupId] ?? [];
    }

    /**
     * @return array<\Upgrade\Application\Dto\ValidatorViolationDto>
     */
    public function getProjectViolations(): array
    {
        return $this->projectViolations;
    }

    public function addProjectViolation(ValidatorViolationDto $violationDto): void
    {
        $this->projectViolations[] = $violationDto;
    }

    public function getReportId(): ?string
    {
        return $this->reportId;
    }

    /**
     * @return $this
     */
    public function setReportId(?string $reportId)
    {
        $this->reportId = $reportId;

        return $this;
    }

    public function getIsStopPropagation(): bool
    {
        return $this->isStopPropagation;
    }

    /**
     * @return $this
     */
    public function setIsStopPropagation(bool $isStopPropagation)
    {
        $this->isStopPropagation = $isStopPropagation;

        return $this;
    }

    public function getLastAppliedReleaseGroup(): ?ReleaseGroupDto
    {
        return end($this->appliedReleaseGroups) ?: null;
    }

    /**
     * @return array<int, \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto>
     */
    public function getAppliedReleaseGroups(): array
    {
        return $this->appliedReleaseGroups;
    }

    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto>
     */
    public function getAppliedSecurityFixedReleaseGroups(): array
    {
        return array_filter(
            $this->appliedReleaseGroups,
            static fn (ReleaseGroupDto $releaseGroup): bool => $releaseGroup->isSecurity(),
        );
    }

    public function addAppliedReleaseGroup(ReleaseGroupDto $appliedReleaseGroup): void
    {
        $this->appliedReleaseGroups[$appliedReleaseGroup->getId()] = $appliedReleaseGroup;
    }

    public function getCurrentReleaseGroup(): ?ReleaseGroupDto
    {
        return $this->currentReleaseGroup;
    }

    public function getCurrentReleaseGroupId(): int
    {
        return $this->currentReleaseGroup ? $this->currentReleaseGroup->getId() : static::UNDEFINED_RELEASE_GROUP_ID;
    }

    public function setCurrentReleaseGroup(ReleaseGroupDto $currentReleaseGroup): void
    {
        $this->currentReleaseGroup = $currentReleaseGroup;
    }

    /**
     * @return array<int, array<\Upgrade\Application\Dto\ViolationDtoInterface>>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @return array<\Upgrade\Application\Dto\ViolationDtoInterface>
     */
    public function getViolationsByReleaseGroupId(int $releaseGroupId): array
    {
        return $this->violations[$releaseGroupId] ?? [];
    }

    public function addViolation(ViolationDtoInterface $violation): void
    {
        $currentReleaseGroupId = $this->getCurrentReleaseGroupId();

        if (!isset($this->violations[$currentReleaseGroupId])) {
            $this->violations[$currentReleaseGroupId] = [];
        }

        $this->violations[$currentReleaseGroupId][] = $violation;
    }

    public function getReleaseGroupStatDto(): ReleaseGroupStatDto
    {
        return $this->releaseGroupStatDto;
    }

    public function setReleaseGroupStatDto(ReleaseGroupStatDto $releaseGroupStatDto): void
    {
        $this->releaseGroupStatDto = $releaseGroupStatDto;
    }

    /**
     * @return array<\Upgrade\Application\Dto\ReleaseGroupFilterResponseDto>
     */
    public function getFilterResponseList(): array
    {
        return $this->filterResponseList;
    }

    public function addFilterResponse(ReleaseGroupFilterResponseDto $responseDto): void
    {
        $this->filterResponseList[] = $responseDto;
    }

    public function isPullRequestSent(): bool
    {
        return $this->isPullRequestSent;
    }

    public function setIsPullRequestSent(bool $isPullRequestSent): void
    {
        $this->isPullRequestSent = $isPullRequestSent;
    }

    public function getTargetBranch(): string
    {
        return $this->targetBranch;
    }

    public function setTargetBranch(string $targetBranch): void
    {
        $this->targetBranch = $targetBranch;
    }

    public function hasErrors(): bool
    {
        return count($this->getBlockers()) > 0
            || count($this->getViolations()) > 0
            || count(
                array_filter(
                    $this->getIntegratorResponseCollection(),
                    static fn (IntegratorResponseDto $response): bool => count($response->getWarnings()) > 0,
                ),
            ) > 0
            || count($this->getProjectViolations()) > 0;
    }

    public function getModelStatisticDto(): ModelStatisticDto
    {
        return $this->modelStatisticDto;
    }

    public function setModelStatisticDto(ModelStatisticDto $modelStatisticDto): void
    {
        $this->modelStatisticDto = $modelStatisticDto;
    }
}
