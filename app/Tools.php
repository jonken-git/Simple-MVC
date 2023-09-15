<?php

function dd(mixed $data, bool $clean = true)
{
    if ($clean) {
        ob_clean();
    }

    try {
        $caller = debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0];
        echo ("<h4>Called by: <b>{$caller['file']}</b><br/>At line: <b>{$caller['line']}</b></h4>");
    } catch (ErrorException) {}

    if (is_array($data)) {
        print_r_pre($data);
    }
    else if (is_object($data)) {
        echo ("<pre>");
        var_dump($data);
        echo ('</pre>');
    } else {
        var_dump($data);
    }
    if($clean) {
        die();
    }
}
function print_r_pre($arr, bool $return = false)
{
    global $g_engine;
    $data = '';
    if (is_array($arr)) {
        $data = is_object($g_engine) ? ($g_engine->_("hits") . ": " . count($arr) . "<br />") : "";
        $data .= '<pre>' . print_r($arr, 1) . '</pre>';
    } else if (is_object($arr)) {
        $reflect = new ReflectionClass($arr);
        $data .= 'Object: ' . $reflect->getShortName() . '<br />Data:';
        $data .= '<pre>' . print_r(get_object_vars($arr), 1) . '</pre>';
        //$data .= '<pre>' . print_r(var_export($arr, 1), 1) . '</pre>';

    } else {
        $data .= '<pre>' . print_r($arr, 1) . '</pre>';
    }

    if ($return)
        return $data;

    echo ($data);
}
function isLocal() : bool
{
    return explode(".", $_SERVER['SERVER_NAME'])[1] == "test";
}
function isOnline() : bool
{
    return !isLocal();
}
