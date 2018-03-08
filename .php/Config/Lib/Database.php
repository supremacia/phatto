<?php
/**
 * Config\Lib\Database
 * PHP version 7
 *
 * @category  Database
 * @package   Config
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      Site <https://phatto.ga>
 */

namespace Config\Lib;

/**
 * Config\Lib\Database Class
 *
 * @category Database
 * @package  Config
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga>
 */

class Database
{
    public static $config = [
        'mysql'=>[
            'dsn'=>'mysql:host=localhost;dbname=phatto;charset=utf8',
            'user'=>'phatto',
            'passw'=>'phatto#123456'],
        'sqlite'=>[
            'dsn'=>'sqlite.db'
        ]
    ];
    
    public static $default = 'mysql';

    //Configuração da tabela de usuário | para sistema de login/gerenciamento
    public static $userTable = [
        'table'=>'usuario',
        'id'=>'id',
        'name'=>'nome',
        'token'=>'token',
        'life'=>'vida',
        'login'=>'login',
        'password'=>'senha',
        'level'=>'nivel',
        'status'=>'status'
    ];
}