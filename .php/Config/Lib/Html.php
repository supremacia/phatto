<?php
/**
 * Config\Lib\Html
 * PHP version 7
 *
 * @category  HtmlView
 * @package   Config
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      Site <https://phatto.ga>
 */

namespace Config\Lib;

/**
 * Config\Lib\Html Class
 *
 * @category HtmlView
 * @package  Config
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga>
 */

class Html
{
    public static $name =             'default';
    public static $mode =             'dev'; //pro|dev
    public static $cacheTime =        20; // 6 hours of life

    public static $pathHtml =         '';
    public static $pathCache =        '';
    public static $pathWww =          '';
    public static $pathStyle =        '';
    public static $pathScript =       '';

    public static $header =           null;
    public static $footer =           null;

    public static $tag =              'x:';
    public static $request =          '';


    /**
     * Boot settings
     */
    public function __construct()
    {
        $root = defined(_ROOTPATH) ? _ROOTPATH : dirname(dirname(__DIR__));
        
        static::$pathWww  = defined('_WWWPATH')    ? _WWWPATH           : $root.'/public';
        static::$pathHtml = defined('_HTMLPATH')   ? _HTMLPATH          : $root.'/.html';
        static::$pathCache = defined('_CACHEPATH') ? _CACHEPATH.'/html' : $root.'/.cache/html';
        static::$mode     = defined('_APPMOD')     ? _APPMOD            : 'dev';

        static::$pathStyle =  static::$pathWww.'/css';
        static::$pathScript = static::$pathWww.'/js';

        static::$header = static::$pathHtml.'/header.html';
        static::$footer = static::$pathHtml.'/footer.html';

        static::$request = \Lib\Router::this()->getRequest();
    }
}
