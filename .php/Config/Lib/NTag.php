<?php
/**
 * Config\NTag
 * PHP version 7
 *
 * @category  Template
 * @package   Config
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      Site <https://phatto.ga/ntag>
 */

namespace Config\Lib;

/**
 * Config\NTag Class
 *
 * @category Template
 * @package  Config
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga/ntag>
 */

class NTag
{
    public static $name      = 'default';
    public static $mode      = 'pro'; //pro|dev
    public static $cacheTime = 21600; // 6 hours of life

    // Paths
    public static $template  = '';
    public static $cache     = '';
    public static $style     = '';
    public static $script    = '';

    // HTML parts
    public static $header    = null;
    public static $footer    = null;

    // External
    public static $plugins   = [];
    public static $url       = '/';

    /**
     * Boot settings
     */
    public function __construct()
    {
        static::$template   = _ROOTPATH.'/.html';
        static::$cache      = _ROOTPATH.'/.html/.cache';
        static::$mode       = 'pro';

        static::$style      = _ROOTPATH.'/public/css';
        static::$script     = _ROOTPATH.'/public/js';

        static::$header     = static::$template.'/header.html';
        static::$footer     = static::$template.'/footer.html';

        static::$url        = _URL;

        static::$plugins['datacontent'] = '\Plugin\Datacontent';
    }
}
