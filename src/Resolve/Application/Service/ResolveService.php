<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Application\Service;

use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\Service\CodebaseService;
use Resolve\Application\Resolves\ResolveInterface;
use Resolve\Domain\Entity\Message;

class ResolveService implements ResolveServiceInterface
{
    /**
     * @var CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @var array<ResolveInterface>
     */
    protected array $resolveChecks;

    /**
     * @param CodebaseService $codebaseService
     * @param array<ResolveInterface> $resolveChecks
     */
    public function __construct(
        CodebaseService $codebaseService,
        array $resolveChecks = []
    ) {
        $this->codebaseService = $codebaseService;
        $this->resolveChecks = $resolveChecks;
    }

    /**
     * @param CodebaseSourceDto $codebaseSourceDto
     *
     *
     * @return Message
     */
    public function resolve(CodebaseSourceDto $codebaseSourceDto): Message
    {
        $result = new Message('');
        foreach ($this->resolveChecks as $resolveCheck) {
            $result = $resolveCheck->run($result, $codebaseSourceDto);
        }

        return $result;
    }
}
