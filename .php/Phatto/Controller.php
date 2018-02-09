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
		$this->View->setPathHtml(__DIR__.'/Html'); //opcional for MVC in module - comment this line for normal MVC (.php/Html/ [FILES].html)
	}


	public function home()
	{		
		$this->View->sendPage();

	}


	public function notFound($requested)
	{	
		//Formatting the message ...
		$msg = 'Page "'._URL.'/'.$requested.'" not found!';

		//Sending as a flash message
		$this->View->sendPage(null, ['flash'=>$msg]);
	}
}