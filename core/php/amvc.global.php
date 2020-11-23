<?php
    $path = explode('\\', __DIR__);
    array_pop($path);
    $path = implode('/', $path);
    define('_PATH_', $path);

    $tab = "    ";
    $newline = "
    ";
    $DB = null;

    
?>