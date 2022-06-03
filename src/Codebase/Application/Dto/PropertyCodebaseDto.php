<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

/**
 * @phpstan-template T of object
 */
class PropertyCodebaseDto
{
    /**
     * @var string|null
     */
    protected ?string $docComment = null;

    /**
     * @return string|null
     */
    public function getDocComment(): ?string
    {
        return $this->docComment;
    }

    /**
     * @param string|null $docComment
     *
     * @return void
     */
    public function setDocComment(?string $docComment): void
    {
        $this->docComment = $docComment;
    }
}
