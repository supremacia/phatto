<?php
/**
 * Phatto\Controller
 * PHP version 7
 *
 * @category  Phatto
 * @package   Example
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      Site <https://phatto.ga>
 */

namespace Phatto;

use Lib\Html;
use Lib\NTag;

/**
 * Phatto\Controller Class
 *
 * @category Phatto
 * @package  Example
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga>
 */

class Controller
{

    function __construct()
    {
        // ... nada
    }


    public function home()
    {
        // instantiating class
        $nt = new NTag;

        // add styles & scripts
        $nt->setStyle('style.css')
           ->setScript('main.js')

        // rendering and sending to the client
           ->render('body')
           ->send();
    }


    public function notFound($requested)
    {
        //Formatting the flash message ...
        $flash = 'Page "'._URL.'/'.$requested.'" not found!';

        // instantiating class
        $nt = new NTag;

        // add styles & scripts
        $nt->setStyle('style.css')
           ->setScript('main.js')

        // rendering and sending to the client
           ->render('body', ['flash'=>$flash])
           ->send();
    }
}
