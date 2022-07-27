<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain\Resolves\NotUnique;

use CodeCompliance\Domain\Checks\Filters\IgnoreListParentFilter;
use Resolve\Domain\AbstractResolveCheck;

class Constant extends AbstractResolveCheck
{
    /**
     * @var string
     */
    public const RECTOR_FILE_PATH = '/rector/constants_to_replace.json';

    /**
     * @var string
     */
    public const RECTOR_DIR = 'rector';

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

        // TODO: Re-use the CodeComplinace code partially here to grab all violation with same rules

        $sources = $this->getCodebaseSourceDto()->getPhpCodebaseSources();
        $filteredSources = $this->filterService->filter($sources, [IgnoreListParentFilter::IGNORE_LIST_PARENT_FILTER]);

        foreach ($filteredSources as $source) {
            $coreParent = $source->getCoreParent();
            $parentConstants = $coreParent ? $coreParent->getConstants() : [];
            $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

            foreach ($source->getConstants() as $nameConstant => $valueConstant) {
                $isConstantUnique = !$parentConstants || !array_key_exists($nameConstant, $parentConstants);
                $hasProjectPrefix = $this->hasProjectPrefix($nameConstant, $projectPrefixes);

                if ($coreParent && $isConstantUnique && !$hasProjectPrefix) {
                    $constantNameRequired = strtoupper((string)reset($projectPrefixes)) . '_' . $nameConstant;

                    /* old approach
                    // preparing violation array, added required prefix and post, by that it wont repeatedly change the same
                    $violations['::' . $nameConstant] = '::' . $constantNameRequired;
                    $violations[' ' . $nameConstant . ' '] = ' ' . $constantNameRequired . ' ';
                    */
                    $violations[$nameConstant] = $constantNameRequired;
                }
            }
        }


        // when do not find any violations
        if (count($violations)<=0) {
            return $this->getName() . ':' . 'Already Resolved';
        }

        /* old approach
        // preparing finder and replacer
        $finder = array_keys($violations);
        $replace = array_values($violations);

        // filter all files one by one to check and replace constant
        foreach ($this->getCodebaseSourceDto()->getPhpFilePaths() as $filePath) {
            $file_contents = file_get_contents($filePath);
            if ($file_contents) {
                $file_contents = str_replace($finder, $replace, $file_contents);
                file_put_contents($filePath, $file_contents);
            }
        }*/


        if (!is_dir(static::RECTOR_DIR)) {
            mkdir(static::RECTOR_DIR, 0777, true);
        }
        if (!file_exists(getcwd() . static::RECTOR_FILE_PATH)) {
            touch(getcwd() . static::RECTOR_FILE_PATH);
        }
        file_put_contents(getcwd() . static::RECTOR_FILE_PATH, json_encode($violations));


        return $this->getName() . ':' . 'Prepared.';
    }
}
