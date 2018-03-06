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
    private $cached =           false;
    private $mode =             'dev'; //pro|dev

    private $pathHtml =         '';
    private $pathHtmlCache =    '';
    private $pathWww =          '';
    private $pathStyle =        '';
    private $pathScript =       '';

    private $header =           null;
    private $footer =           null;

    private $forceCompress =    false;
    private $tag =              'x:';


    /**
     * Boot settings
     */
    function __construct()
    {
        $this->pathWww  = defined('_WWWPATH')   ? _WWWPATH  : dirname(dirname(__DIR__)).'/public';
        $this->pathHtml = defined('_HTMLPATH')  ? _HTMLPATH : dirname(dirname(__DIR__)).'/.html';
        $this->mode     = defined('_APPMOD')    ? _APPMOD   : 'dev';
        
        $this->pathHtmlCache = $this->pathHtml.'/cache';
        $this->pathStyle = $this->pathWww.'/css';
        $this->pathScript = $this->pathWww.'/js';

        $this->header = $this->pathHtml.'/header.html';
        $this->footer = $this->pathHtml.'/footer.html';
    }

    /*
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
