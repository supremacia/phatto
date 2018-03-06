<?php

// Defaults
error_reporting(E_ALL ^ E_STRICT ^ E_WARNING);
setlocale(LC_ALL, 'pt_BR');
date_default_timezone_set('America/Sao_Paulo');


// Development only...
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('track_errors', '1');


// Constants
define('_PHPPATH',  __DIR__.'/.php');    // Path to PHP application files
define('_HTMLPATH', __DIR__.'/.html');  // Path to HTML files (templates)
define('_WWWPATH',  __DIR__.'/public');  // Path to public folder
define('_APPMOD',  'dev');               // Application modes: dev|pro
//define('_URL',   'http://localhost'); // force, but the router creates this


// Composer ( & all others ) autoload --
$autoload = _PHPPATH.'/Composer/autoload.php';
if (file_exists($autoload)) {
    require $autoload;
} else {
    spl_autoload_register(
        function ($class) {
            $file = str_replace('\\', '/', _PHPPATH.'/'.$class.'.php');
            if (file_exists($file)) {
                require $file;
            }
        }
    );
}


// Running Router
Lib\Router::this()->run();






/*
    --------------------------
    That's all for now, folks!
    --------------------------

*/
















//ATT: ----------------------------------------------------- <<
//     Debug only when in development
//     In production, please DELETE from this comment.
function e($v, $log = '')
{
    exit(p($v, $log, false));
}
function p($v, $log = '', $show = true)
{
    $p = '<div style="text-family:sans-serif;width:fit-content"><pre style="background:#EFE;color:#090;border-radius:5px;padding:15px;margin:20px;box-shadow:0 5px 30px rgba(0,0,0,.3);width:fit-content"><h3 style="text-align:center;margin:-16px -16px 10px -16px;padding:10px;background:#039;color:#FFF">'.$log.'</h3>'.print_r($v, true).'</pre></div>';

    if (!$show) {
        return $p;
    }
    echo $p;
}
