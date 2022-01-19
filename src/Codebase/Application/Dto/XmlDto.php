<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

class XmlDto implements CodebaseInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    public $name;

    /**
     * @var array<string>
     */
    protected $childElements = [];

    /**
     * @param string $path
     * @param string $name
     */
    public function __construct(string $path = '', string $name = '')
    {
        $this->path = $path;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $childElement
     *
     * @return $this
     */
    public function addChildElement(string $childElement)
    {
        $this->childElements[] = $childElement;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getChildElements(): array
    {
        return $this->childElements;
    }
}
