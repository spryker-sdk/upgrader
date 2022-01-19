<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceFinder;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeFinder;

class ClassNodeFinder
{
    /**
     * @param array $nodes
     *
     * @return \PhpParser\Node|null
     */
    public function findClassNode(array $nodes): ?Node
    {
        return (new NodeFinder())->findFirst($nodes, function (Node $node) {
            return $node instanceof Class_ || $node instanceof Interface_;
        });
    }
}
