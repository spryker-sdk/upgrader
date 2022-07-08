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
        $violations = [];

        // Re-use the CodeComplinace part here
        foreach ($this->getCodebaseSourceDto()->getPhpCodebaseSources() as $source) {
            $coreParent = $source->getCoreParent();
            $parentConstants = $coreParent ? $coreParent->getConstants() : [];
            $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

            foreach ($source->getConstants() as $nameConstant => $valueConstant) {
                $isConstantUnique = !$parentConstants || !array_key_exists($nameConstant, $parentConstants);
                $hasProjectPrefix = $this->hasProjectPrefix($nameConstant, $projectPrefixes);

                if ($coreParent && $isConstantUnique && !$hasProjectPrefix) {
                    $constantNameRequired = strtoupper((string)reset($projectPrefixes)) . '_' . $nameConstant;

                    // preparing violation array, added required prefix and post, by that it wont repeatedly change the same
                    $violations['::' . $nameConstant] = '::' . $constantNameRequired;
                    $violations[' ' . $nameConstant . ' '] = ' ' . $constantNameRequired . ' ';
                    //$violations[$nameConstant] = $constantNameRequired;
                }
            }
        }

        // if do not find any violations
        if (count($violations)<=0) {
            return $this->getName() . ':' . 'Already Resolved';
        }

        // preparing finder and replacer
        $finder = array_keys($violations);
        $replace = array_values($violations);

        //print_r($this->getCodebaseSourceDto()->getPhpFilePaths());
        foreach ($this->getCodebaseSourceDto()->getPhpFilePaths() as $filePath) {
            $file_contents = file_get_contents($filePath);
            if ($file_contents) {
                $file_contents = str_replace($finder, $replace, $file_contents);
                file_put_contents($filePath, $file_contents);
            }
        }

        return $this->getName() . ':' . 'Resolved';
    }
}
