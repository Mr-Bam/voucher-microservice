<?php

function dd($data = '')
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function d($data = '')
{
    dd($data);
    stackTrace();
    die();
}

function stackTrace() {
    $e = new \Exception;
    var_dump($e->getTraceAsString());
}

function ci($object)
{
    dd(get_class($object));
    dd(get_class_methods(get_class($object)));
    d();
}

