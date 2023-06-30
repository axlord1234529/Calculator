<?php
define('SERVER_ROOT', $_SERVER['DOCUMENT_ROOT'].'/Score-Calculator/');
spl_autoload_register(function($className) {
    $fileName = strtolower($className).'.php';
    $file = SERVER_ROOT.'classes/'.$fileName;
    if(file_exists($file))
    {
        include_once($file);
    }
    else
    {
        die("File '$fileName' containing class '$className' not found.");
    }
});