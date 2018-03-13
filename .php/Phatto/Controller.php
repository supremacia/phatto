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
    private $ntag = null;

    function __construct()
    {
        // instantiating class
        $this->ntag = new NTag;

        // add styles & scripts
        $this->ntag->setStyle('style.css')
                   ->setScript('main.js');
    }


    public function home()
    {
        // rendering and sending to the client
        $this->ntag->render('body')
                   ->send();
    }

    public function manual()
    {
        // rendering and sending to the client
        $this->ntag->render('manual/index')
                   ->send();
    }

    public function router()
    {
        // rendering and sending to the client
        $this->ntag->setStyle('medium-editor.min.css')
                   ->setScript('medium-editor.min.js')
                   ->setScript('manual.js')
                   ->render('manual/router')
                   ->send();
    }

    public function database()
    {
        // rendering and sending to the client
        $this->ntag->render('manual/database')
                   ->send();
    }

    public function ntag()
    {
        // rendering and sending to the client
        $this->ntag->render('manual/ntag')
                   ->send();
    }

    public function about()
    {
        // rendering and sending to the client
        $this->ntag->render('about')
                   ->send();
    }

    public function privacy()
    {
        // rendering and sending to the client
        $this->ntag->render('privacy')
                   ->send();
    }

    public function notFound($requested)
    {
        //Formatting the flash message ...
        $flash = 'Page "'._URL.'/'.$requested.'" not found!';

        // rendering and sending to the client
        $this->ntag->render('body', ['flash'=>$flash])
                   ->send();
    }
}
