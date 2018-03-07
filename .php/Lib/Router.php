<?php
/**
 * Lib\Router
 * PHP version 7
 *
 * @category  Access
 * @package   Library
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      Site <https://phatto.ga>
 */
namespace Lib;

/**
 * Router Class
 *
 * @category Access
 * @package  Library
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga>
 */
class Router
{
    private $autorun = true;
    private $url = false;
    private $http = '';
    private $base = '';
    private $request = false;
    private $routes = [];
    private $params = [];
    private $args = []; //extra params in 'respond'
    private $all = [];
    private $method = 'GET'; //CLI, GET, POST, DELETE, PUT, PATCH, OPTIONS, HEAD
    private $separator = '::';
    private $controller = '';
    private $action = '';

    //WEB
    private $defaultController = 'App';
    private $defaultAction = 'pageNotFound';
    private $namespacePrefix = ''; //namespace prefix for MVC systems - ex.: '\Controller'

    //CLI
    private $defaultCliController = 'Main';
    private $defaultCliAction = 'cliHelp';
    private $namespaceCliPrefix = 'Devbr\Cli';
    
    //Statics
    private static $node = null;
    private static $ctrl = null;
    
    //GETs -----------------------------------------------------------------
    public function getUrl()
    {
        return $this->url;
    }
    public function getHttp()
    {
        return $this->http;
    }
    public function getBase()
    {
        return $this->base;
    }
    public function getRequest()
    {
        return $this->request;
    }
    public function getRoutes()
    {
        return $this->routes;
    }
    public function getAll()
    {
        return $this->all;
    }
    public function getMethod()
    {
        return $this->method;
    }
    
    public function getController()
    {
        return $this->controller;
    }
    public function getAction()
    {
        return $this->action;
    }
    public function getSeparator()
    {
        return $this->separator;
    }
    public function getParams()
    {
        return count($this->params) > 0 ? $this->params : null;
    }
    //SETs -----------------------------------------------------------------
    public function setSeparator($v)
    {
        $this->separator = $v;
        return $this;
    }
    
    public function setDefaultController($v)
    {
        $this->defaultController = trim(str_replace('/', '\\', $v), '\\/ ');
        return $this;
    }
    
    public function setDefaultAction($v)
    {
        $this->defaultAction = trim($v, '\\/ ');
        return $this;
    }
    
    public function setNamespacePrefix($v)
    {
        $this->namespacePrefix = $v === '' ? '' : '\\'.trim(str_replace('/', '\\', $v), '\\/ ');
        return $this;
    }

    //CLI
    public function setDefaultCliController($v)
    {
        $this->defaultCliController = trim(str_replace('/', '\\', $v), '\\/ ');
        return $this;
    }
    
    public function setDefaultCliAction($v)
    {
        $this->defaultCliAction = trim($v, '\\/ ');
        return $this;
    }
    
    public function setNamespaceCliPrefix($v)
    {
        $this->namespaceCliPrefix = $v === '' ? '' : '\\'.trim(str_replace('/', '\\', $v), '\\/ ');
        return $this;
    }
    
    /**
     * Constructor
     */
    public function __construct(
        $autorun = true,
        $request = null,
        $url = null
    ) {
        if ($autorun === false) {
            $this->autorun = false;
        }
        
        if ($request !== null) {
            $this->request = $request;
        }
        
        if ($url !== null) {
            $this->url = $url;
        }
        
        $this->method = $this->requestMethod();
        $this->mount();

        //Saving this object in static node (for future static access)
        if (!is_object(static::$node)) {
            static::$node = $this;
        }
    }
    /**
     * Singleton instance
     *
     */
    public static function this()
    {
        if (is_object(static::$node)) {
            return static::$node;
        }
        //else...
        list($autorun, $request, $url) = array_merge(func_get_args(), [null, null, null]);
        return static::$node = new static($autorun, $request, $url);
    }
    
    /**
     *  Get Controller Object
     */
    public static function getCtrl()
    {
        return static::$ctrl;
    }


    public function loadConfig()
    {   
        // Nome da classe de configuração.
        $config = '\\Config\\'.__CLASS__;
        if (class_exists($config)) {
            // Carrega os parametros na classe atual.
            foreach ($this as $key=>$val) {
                if (isset($config::$$key)) {
                    $this->$key = $config::$$key;
                }
            }
        }
        return $this;
    }
    
    /**
     * Make happen...
     *
     */
    public function run()
    {
        //Resolve request
        $this->resolve();
        
        //If is a CALLBACK...
        if (is_object($this->controller)) {
            exit(call_user_func_array($this->controller, [$this->request, $this->params, $this->args]));
        }
        if ($this->controller === null) {
            $this->controller = $this->method == 'CLI' ? $this->defaultCliController : $this->defaultController;
        }
        if ($this->action === null) {
            $this->action = $this->method == 'CLI' ? $this->defaultCliAction : $this->defaultAction;
        }

        //Name format to Controller namespace
        $ctrl = $this->formatePrsr4Name($this->controller, $this->method == 'CLI' ? $this->namespaceCliPrefix : $this->namespacePrefix);
        
        //Save the controller...
        $this->controller = $ctrl;
        
        //Check whether to automatically run the Controller
        //      or return this object
        if (!$this->autorun) {
            return $this;
        }
        
        //Instantiate the controller
        if (class_exists($ctrl)) {
            static::$ctrl = new $ctrl($this->params, $this->request);
        //IN CLI mode finish in this point: Cli\Main::__construct return to CMD.
        } else {
            if ($this->method != 'CLI') {
                header("HTTP/1.0 404 Not Found");
                exit('Page not Found!');
            }
            exit("\nController not found!");
        }
        
        //Seeking for the METHOD...
        if (!method_exists(static::$ctrl, $this->action)) {
            $this->action = $this->method == 'CLI' ? $this->defaultCliAction : $this->defaultAction;
            if (!method_exists(static::$ctrl, $this->action)) {
                if ($this->method != 'CLI') {
                    header("HTTP/1.0 404 Not Found");
                    exit('Page not Found!');
                }
                exit();
            }
        }
        
        //Call action
        return call_user_func_array(
            [static::$ctrl, $this->action],
            [$this->request, $this->params, $this->args]
        );
    }

    /**
     * Resolve routes
     *
     */
    public function resolve()
    {
        if ($this->method == 'CLI') {
            $route = $this->searchCliRoute();
        } else {
            //first: serach in ALL
            $route = $this->searchRoute($this->all);
            //now: search for access method
            if ($route === false && isset($this->routes[$this->method])) {
                $route = $this->searchRoute($this->routes[$this->method]);
            }
        }
        //not match...
        if ($route === false) {
            $route['controller'] = $route['action'] = $route['params'] = $route['args'] = $route['request'] = null;
        }
        //set params
        $this->controller = $route['controller'];
        $this->action = $route['action'];
        $this->params = $route['params'];
        $this->args = $route['args'];
        //out with decoded router OR all null
        return $route;
    }
    /**
     * Insert/config routes
     *
     */
    public function respond(
        $method = 'all',
        $request = '',
        $controller = null,
        $action = null,
        $args = null
    ) {
        $method = strtoupper(trim($method));
        //Para sintaxe: CONTROLLER::ACTION
        if (!is_object($controller) && strpos($controller, $this->separator) !== false) {
            $a = explode($this->separator, $controller);
            $controller = isset($a[0]) ? $a[0] : null;
            $action = isset($a[1]) ? $a[1] : null;
        }
        if ($method == 'ALL') {
            $this->all[] = ['request' => trim($request, '/'), 'controller' => $controller, 'action' => $action, 'args' => $args];
        } else {
            foreach (explode('|', $method) as $mtd) {
                $this->routes[$mtd][] = ['request' => trim($request, '/'), 'controller' => $controller, 'action' => $action, 'args' => $args];
            }
        }
        return $this;
    }
    /**
     * Mount
     */
    private function mount()
    {
        if ($this->method === 'CLI') {
            global $argv;

            if (isset($argv[0]) && $argv[0] == 'index.php') {
                array_shift($argv);
            }

            $this->request = $argv;

            return;
        }
        //Detect SSL access
        if (!isset($_SERVER['SERVER_PORT'])) {
            $_SERVER['SERVER_PORT'] = 80;
        }
        $this->http = (isset($_SERVER['HTTPS']) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == 1 || $_SERVER['SERVER_PORT'] == 443)) ? 'https://' : 'http://';
        //What's base??!
        $this->base = isset($_SERVER['PHAR_SCRIPT_NAME']) ? dirname($_SERVER['PHAR_SCRIPT_NAME']) : rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']), ' /');
        if ($_SERVER['SERVER_PORT'] != 80  && $_SERVER['SERVER_PORT'] != 443) {
            $this->base .= ':' . $_SERVER['SERVER_PORT'];
        }
        
        //URL & REQST Constants:
        if ($this->request === false) {
            $this->request = urldecode(isset($_SERVER['REQUEST_URI']) ? urldecode(trim(str_replace($this->base, '', trim($_SERVER['REQUEST_URI'])), ' /')) : '');
        }
        defined('_RQST') || define('_RQST', $this->request);

        if ($this->url === false) {
            $this->url = isset($_SERVER['SERVER_NAME']) ? $this->http . $_SERVER['SERVER_NAME'] . $this->base : '';
        }
        defined('_URL') || define('_URL', $this->url);
        
        //Load configurations
        $this->loadConfig();
    }


    /**
     * [formatePrsr4Name description]
     *
     * @param  [type] $name   [description]
     * @param  string $prefix [description]
     *
     * @return [type]         [description]
     */
    private function formatePrsr4Name($name, $prefix = '')
    {
        $tmp = explode('\\', str_replace('/', '\\', $name));
        $name = $prefix;
        foreach ($tmp as $tmp1) {
            $name .= '\\'.ucfirst($tmp1);
        }

        return $name;
    }

    /**
     * [searchCliRoute description]
     *
     * @return [type] [description]
     */
    private function searchCliRoute()
    {
        $request = $this->request;
        $this->request = implode(' ', $this->request);
    
        $route = ['request'=>$request, 'controller'=>false, 'action'=>null, 'params'=>null, 'args'=>null];

        if (isset($request[0])) {
            $tmp = explode($this->separator, $request[0]);
            if (isset($tmp[0])) {
                $route['controller'] = $tmp[0];
            }
            if (isset($tmp[1])) {
                $route['action'] = $tmp[1];
            }
            array_shift($request);

            if (count($request) > 0) {
                $route['params'] = $request;
            }
        }

        return $route;
    }


    /**
     * Search for valide route
     *
     * @params
     */
    private function searchRoute($routes)
    {
        foreach ($routes as $route) {
            if ($route['controller'] === null
              || !preg_match_all(
                  '#^' . $route['request'] . '$#',
                  $this->request,
                  $matches,
                  PREG_SET_ORDER
              )
                  ) {
                continue;
            }
            $route['params'] = array_slice($matches[0], 1);
            return $route;
        }
        //não existe rotas
        return false;
    }
    /**
     * Get all request headers
     * @return array The request headers
     */
    private function requestHeaders()
    {
        // getallheaders available, use that
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        // getallheaders not available: manually extract 'm
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
    /**
     * Get the request method used, taking overrides into account
     * @return string The Request method to handle
     */
    private function requestMethod()
    {
        if (php_sapi_name() === 'cli') {
            return 'CLI';
        }
        // Take the method as found in $_SERVER
        $method = $_SERVER['REQUEST_METHOD'];
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } // If it's a POST request, check for a method override header
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = $this->requestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }
        return $method;
    }
}
