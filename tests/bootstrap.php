<?php

use Symfony\Component\ErrorHandler\ErrorHandler;

define('APPLICATION_ROOT_DIR', dirname(__DIR__, 1));

require dirname(__DIR__) . '/vendor/autoload.php';
set_exception_handler([new ErrorHandler(), 'handleException']);
