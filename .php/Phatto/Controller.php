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
 * @link      Author contacts <http://billrocha.tk>
 */

namespace Phatto;

use Lib\Html;

/**
 * Phatto\Controller Class
 *
 * @category Phatto
 * @package  Example
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Author contacts <http://billrocha.tk>
 */

class Controller
{
    private $View;


    function __construct()
    {
        $this->View = new Html;

        // Opcional for MVC in module
        //  --> comment this line for normal MVC (.php/Html/ [FILES].html)
        $this->View->setPathHtml(__DIR__.'/Html');

        // Styles & Javascripts
        $this->View->insertStyles(['style']);
        $this->View->insertScripts(['main']);

        // Use Blade
        $this->View->setBlade(true);
      

        //DELETE
        $this->View->val('Teste', 'Conteúdo do Teste');
        
        $this->View->insertBlock('block', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.');

        $this->View->val('teste', ['1'=>'Valor 1', '2'=>'Valor 2', '3'=>'Valor 3','-default-'=>'2']);
    }


    public function home()
    {
        $this->View->render('phatto')
               ->send();
    }


    public function notFound($requested)
    {
        //Formatting the flash message ...
        $flash = 'Page "'._URL.'/'.$requested.'" not found!';

        //Sending
        $this->View->render('phatto', ['flash'=>$flash])
                   ->send();
    }
}
