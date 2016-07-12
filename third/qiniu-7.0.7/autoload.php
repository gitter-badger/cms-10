<?php

function classLoaderqiniu($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/src/' . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoaderqiniu');

require_once  __DIR__ . '/src/Qiniu/functions.php';
