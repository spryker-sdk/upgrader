<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Entity;

use SprykerSdk\SdkContracts\Violation\ViolationReportConverterInterface;

class Violation implements ViolationReportConverterInterface
{
    /**
     * @var string
     */
    protected string $id = '';

    /**
     * @var string
     */
    protected string $message = '';

    /**
     * @var bool
     */
    protected bool $isFixable = false;

    /**
     * @var string
     */
    protected string $producedBy = '';

    /**
     * @var string|null
     */
    protected ?string $priority;

    /**
     * @var string|null
     */
    protected ?string $class;

    /**
     * @var string|null
     */
    protected ?string $method;

    /**
     * @var int|null
     */
    protected ?int $startLine;

    /**
     * @var int|null
     */
    protected ?int $endLine;

    /**
     * @var int|null
     */
    protected ?int $startColumn;

    /**
     * @var int|null
     */
    protected ?int $endColumn;

    /**
     * @var array
     */
    protected array $additionalAttributes;

    /**
     * @param string $id
     * @param string $message
     * @param string $producedBy
     * @param array $additionalAttributes
     * @param bool $isFixable
     * @param string|null $class
     * @param string|null $method
     * @param string|null $priority
     * @param int|null $startLine
     * @param int|null $endLine
     * @param int|null $startColumn
     * @param int|null $endColumn
     */
    public function __construct(
        string $id = '',
        string $message = '',
        string $producedBy = '',
        array $additionalAttributes = [],
        bool $isFixable = false,
        ?string $class = null,
        ?string $method = null,
        ?string $priority = null,
        ?int $startLine = null,
        ?int $endLine = null,
        ?int $startColumn = null,
        ?int $endColumn = null
    ) {
        $this->id = $id;
        $this->message = $message;
        $this->producedBy = $producedBy;
        $this->additionalAttributes = $additionalAttributes;
        $this->isFixable = $isFixable;
        $this->class = $class;
        $this->method = $method;
        $this->priority = $priority;
        $this->startLine = $startLine;
        $this->endLine = $endLine;
        $this->startColumn = $startColumn;
        $this->endColumn = $endColumn;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isFixable(): bool
    {
        return $this->isFixable;
    }

    /**
     * @return string
     */
    public function producedBy(): string
    {
        return $this->producedBy;
    }

    /**
     * @return string|null
     */
    public function priority(): ?string
    {
        return $this->priority;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @return int|null
     */
    public function getStartLine(): ?int
    {
        return $this->startLine;
    }

    /**
     * @return int|null
     */
    public function getEndLine(): ?int
    {
        return $this->endLine;
    }

    /**
     * @return int|null
     */
    public function getStartColumn(): ?int
    {
        return $this->startColumn;
    }

    /**
     * @return int|null
     */
    public function getEndColumn(): ?int
    {
        return $this->endColumn;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getAdditionalAttributes(): array
    {
        return $this->additionalAttributes;
    }
}
