<?php

// Defaults
error_reporting(E_ALL ^ E_STRICT ^ E_WARNING);
setlocale (LC_ALL, 'pt_BR');
date_default_timezone_set('America/Sao_Paulo');


// Development only...
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('track_errors', '1');


// Constants
define('_PHPPATH',  __DIR__.'/.php');  	// Path to PHP application files
define('_HTMLPATH', _PHPPATH.'/Html');	// Path to HTML files (templates)
define('_WWWPATH',  __DIR__.'/public');	// Path to public folder
define('_APPMOD',   'dev');				// Application modes: dev|pro

// define('_URL', 'https://example.com'); // force, but the router creates this


// Composer ( & all others ) autoload --
include _PHPPATH.'/Composer/autoload.php';


//Optional and simple autoload, when not using Composer
/*
spl_autoload_register(
	function ($class) {
		$file = str_replace('\\', '/', _PHPPATH.'/'.$class.'.php');
		if(file_exists($file)) require $file;
	}
);
*/


// Running Router
( new Lib\Router)->run();
