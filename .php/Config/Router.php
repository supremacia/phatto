<?php
/**
 * Config\Router
 * PHP version 7
 *
 * @category  Router
 * @package   Config
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      Author contacts <http://billrocha.tk>
 */

namespace Config;

/**
 * Config\Router Class
 *
 * @category Router
 * @package  Config
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Author contacts <http://billrocha.tk>
 */

class Router
{
    function __construct($router)
    {
        //Defaults controller :: action (for page not found)
        $router->setDefaultController('Phatto/Controller')
               ->setDefaultAction('notFound');


        //Defaults routers
        $router->respond('get', '/', 'Phatto/Controller::home')
               ->respond('get', '/teste', 'Lib/Html', 'show', 'teste')

              /* Yours routes here

                ->respond('method', 'URL', 'Namespace/Class::action')

                method: get (or post, or etc...) 
                    or: get|post|etc 
                    or: all (for all :P)

              */

               ->respond('options|head', '.*',
                    function () {
                        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT, PATCH, HEAD');
                        exit();
                    });
        
        //Load others routes
        //require_once __DIR__.'/router.staticPages.php';
    }
}


/* Description:

        $router->respond( <type>, <request>, <controller>, [<action>]);
        
            <type>:       A string with the following methods: "all", "get", "post", "delete", "put", "patch". 
                          Or specify a specific group: "get|post|delete".
                          
            <request>:    String of the requested URI - ex.: "about/me" ==> http://site.com/about/me
            
            <controller>: Class (object) to manage the request. 
                          Name must be a complete string, with NAMESPACE + CLASSNAME. Ex.: "Devbr\User".
                          Alternatively you can use the following format: "controller::action" - ex.: "Devbr\User::login".                          
                          The Controller can also be an anonymous function that receives (or not) 
                          parameters of the regular expression in <request>. 
                          Ex.: $router->respond('get', '/(*)', function($path){exit($path);});
                           -- if request "http://site.com/test/me", print in screen "test/me".
            
            <action>:     Optional form to indicate an action. Ex.: "login"
*/


/* SAMPLES:
    

        $router->respond('get', '/', 'Resource\Main::index')

               ->respond('get', 'login', 'Blog\Page::login')
               ->respond('get', 'blog', 'Devbr\Install\Page::index')

               ->respond('get', 'blog/e/(?<id>(.*)?)', 'Blog\Page::edit')
               ->respond('get', 'blog/(?<id>.*?)', 'Blog\Page::view')

               //AJAX ----------
               ->respond('post', 'blog/save', 'Blog\Ajax::save')
               ->respond('post', 'blog/checklink', 'Blog\Ajax::checkLink')

               ->respond('post', 'blog/delete/(?<id>(\d+)?)', 'Blog\Ajax::delete')
               ->respond('post', 'blog/upload/(?<id>(\d+)?)', 'Blog\Ajax::upload')


            Considere a url:
            http://localhost/loja/903/Camisa Polo Marca XXX/ldfnld-n0p/+=dlknferçlkm/dsfdfdsjd//

            Captura:
            ['id'] => 903
            ['produto'] => Camisa Polo MArca XXX

            Ignora tudo mais...

            Veja a função "teste" da classe em "/.php/Site/Front.php"
            Segue a configuração da rota, abaixo:

               ->respond('get', '/loja/(?<id>.*?)/(?<produto>[^/]*).*', 'Resource\Main::indexTest')
               

            A mesma configuração, porém com parametros (índice) NUMÉRICOS:
            http://localhost/loja2/903/Camisa Polo Marca XXX/ldfnld-n0p/+=dlknferçlkm/dsfdfdsjd//

                
               ->respond('get', '/loja2/(.*?)/([^/]*).*', 'Resource\Main::indexTest')


            Usando uma função anônima diretamente na configuração do Router
            http://localhost/fac/categoria/pergunta

               ->respond('get', '/fac/(.*?)/([^/]*).*',

                            function ($type, $user) {
                                echo '<h1>Função anônima</h1>
                                      <p><b>Request URI:</b> '.$type.'<br>
                                      <b>Parametros:</b><pre>'.print_r($user, true).'</pre></p>';
                            })


            Usando uma função anônima para mostrar uma página HTML ESTÁTICA
            http://localhost/about                    

               ->respond('get', '/about',

                            function () {
                                include _HTML.'Static/about.html';
                            });
*/
