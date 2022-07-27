<?php declare(strict_types=1);

use Rector\Renaming\Rector\ConstFetch\RenameConstantRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;
use Resolve\Domain\Resolves\NotUnique\Method;
use Resolve\Domain\Resolves\NotUnique\Constant;
use Rector\Core\Configuration\Option;


// here we can define, what sets of rules will be applied
return static function (RectorConfig $rectorConfig): void {
    // set timeout if needed
    $rectorConfig->parallel(1000);

    $PROJECT_DIR_NAME = '../b2c-demo-shop';
    $PROJECT_SRC_DIR_PATH = '/src';

    // paths to refactor; solid alternative to CLI arguments
    //$rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);
    $rectorConfig->paths([$PROJECT_DIR_NAME . $PROJECT_SRC_DIR_PATH]);

    // tip: use "SetList" class to autocomplete sets
    //$rectorConfig->sets([
    //    SetList::CODE_QUALITY
    //]);

    // register single rule
    //$rectorConfig->rule(TypedPropertyRector::class);

    // Rename Constants
    $fileConstantContents = file_get_contents($PROJECT_DIR_NAME . Constant::RECTOR_FILE_PATH);
    $dataConstant = json_decode((string)$fileConstantContents, true);
    $rectorConfig->ruleWithConfiguration(RenameConstantRector::class, $dataConstant);


    // Rename Methods
    $fileContents = file_get_contents($PROJECT_DIR_NAME . Method::RECTOR_FILE_PATH);
    $dataMethods = json_decode((string)$fileContents, true);
    $renameMethods=[];
    foreach ($dataMethods as $method) {
        $renameMethods[] = new MethodCallRename($method['className'], $method['methodName'], $method['desiredMethodName']);
    }
    $rectorConfig->ruleWithConfiguration(RenameMethodRector::class,$renameMethods);

};
