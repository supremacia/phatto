<?php
/**
 * Config\Html
 * PHP version 7
 *
 * @category  HtmlView
 * @package   Config
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      Author contacts <http://billrocha.tk>
 */

namespace Config;

/**
 * Config\Html Class
 *
 * @category HtmlView
 * @package  Config
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Author contacts <http://billrocha.tk>
 */

class Html
{
    private $name =             'default';
    private $mode =             'dev'; //pro|dev
    private $cacheTime =        20; // 6 hours of life

    private $pathHtml =         '';
    private $pathCache =    '';
    private $pathWww =          '';
    private $pathStyle =        '';
    private $pathScript =       '';

    private $header =           null;
    private $footer =           null;

    private $tag =              'x:';
    private $request =          '';


    /**
     * Boot settings
     */
    function __construct()
    {
        $root = defined(_ROOTPATH) ? _ROOTPATH : dirname(dirname(__DIR__));
        
        $this->pathWww  = defined('_WWWPATH')    ? _WWWPATH           : $root.'/public';
        $this->pathHtml = defined('_HTMLPATH')   ? _HTMLPATH          : $root.'/.html';        
        $this->pathCache = defined('_CACHEPATH') ? _CACHEPATH.'/html' : $root.'/.cache/html';
        $this->mode     = defined('_APPMOD')     ? _APPMOD            : 'dev';

        $this->pathStyle = $this->pathWww.'/css';
        $this->pathScript = $this->pathWww.'/js';

        $this->header = $this->pathHtml.'/header.html';
        $this->footer = $this->pathHtml.'/footer.html';

        $this->request = \Lib\Router::this()->getRequest();
    }

    /**
     * Return all parameters
     */
    public function getParams()
    {
        foreach ($this as $k => $v) {
            $cfg[$k] = $v;
        }
        return $cfg;
    }
}
