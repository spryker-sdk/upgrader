<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain\Resolves\NotUnique;

use Resolve\Domain\AbstractResolveCheck;

class Constant extends AbstractResolveCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:Constant';
    }

    /**
     * @return string
     */
    public function getResult(): string
    {


        $testFile = '/data/src/Pyz/Yves/HelloWorld/Plugin/Provider/HelloWorldRouteProviderPlugin.php';
        $file_contents = file_get_contents($testFile);
        $file_contents = str_replace("MY_CONSTANT","PYZ_MY_CONSTANT",$file_contents);
        file_put_contents($testFile,$file_contents);
exit;
        print_r($this->getCodebaseSourceDto()->getPhpFilePaths());

        $violations = [];

        foreach ($this->getCodebaseSourceDto()->getPhpCodebaseSources() as $source) {
            $coreParent = $source->getCoreParent();
            $parentConstants = $coreParent ? $coreParent->getConstants() : [];
            $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

            foreach ($source->getConstants() as $nameConstant => $valueConstant) {
                $isConstantUnique = !$parentConstants || !array_key_exists($nameConstant, $parentConstants);
                $hasProjectPrefix = $this->hasProjectPrefix($nameConstant, $projectPrefixes);

                if ($coreParent && $isConstantUnique && !$hasProjectPrefix) {
                    $violations[$nameConstant] = strtoupper((string)reset($projectPrefixes)).'_'.$nameConstant;
                }
            }
        }

        print_r($violations);

        return $this->getName() . ':' . 'Resolved';
    }
}
