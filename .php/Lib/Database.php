<?php
/**
 * Devbr\Database
 * PHP version 7
 *
 * @category  Database
 * @package   Data
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2016 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      http://dbrasil.tk/devbr
 */
namespace Lib;
use PDO;

/**
 * Database Class
 *
 * @category Database
 * @package  Data
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     http://dbrasil.tk/devbr
 */
class Database
{
    private $config =   null;
    
    private $conn =     null;
    private $sql    =   null;
    
    private $result =   null;
    private $rows =     0;
    
    private $error =    [];

    /**
     * Constructor
     *
     * @param array $config configurations
     */
    function __construct($config = null)
    {
        if (is_array($config)) {
            $this->config = $config;
        } elseif (method_exists('\Config\Database', 'get')) {
            $this->config = \Config\Database::get($config);
        } else {
            trigger_error('DataBase configurations not found!');
        }
    }

    /**
     * Connector
     *
     * @param string $alias Alias for database connection
     *
     * @return object       This PDO resource
     */
    function connect($alias = null)
    {
        if ($this->conn == null) {
            try {
                $this->conn = new PDO(
                    $this->config['dsn'],
                    $this->config['user'],
                    $this->config['passw']
                );

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                trigger_error('Data base not connected!');
            }
        }
        if (!is_object($this->conn)) {
            trigger_error('I can not connect to the database', E_USER_ERROR);
        }
        return $this->conn;
    }

    /**
     * Query
     *
     * @param string $sql   SQL query
     * @param array  $parms Array of params to "prepare" statment
     * @param string $alias Alias of the database connection
     *
     * @return bool|object   False or Row class of results
     */
    function query($sql, $parms = array(), $alias = null)
    {
        $this->sql = $sql;
        $sth = $this->connect($alias)->prepare($sql);
        $sth->execute($parms);
        $this->rows = $sth->rowCount();
        $this->error[$sql] = $sth->errorInfo();

        if ($sth->columnCount() > 0) {
            return $this->result = $sth->fetchAll(PDO::FETCH_CLASS, __NAMESPACE__.'\Row', [$this->sql, $parms]);
        } else {
            $this->result = false;
            return $this->rows;
        }
    }

    /**
     * Get results
     *
     * @return object ROW object
     */
    function result()
    {
        if ($this->result == null || count($this->result) == 0) {
            return false;
        }
        return $this->result;
    }
    
    /**
     * Clear data
     *
     * @return void
     */
    function clear()
    {
        $this->result = new Row;
    }

    /**
     * Get error
     *
     * @return string Error description
     */
    function getError()
    {
        return $this->error;
    }

    /**
     * Get rows
     *
     * @return integer number of rows affected by the last DELETE, INSERT or UPDATE
     */
    function getRows()
    {
        return $this->rows;
    }

    /**
     * Get SQL
     *
     * @return string return last SQL string
     */
    function getSql()
    {
        return $this->sql;
    }
}

/**
 * Row Class
 *
 * @category Database
 * @package  Data
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     http://dbrasil.tk/devbr
 */
class Row
{
    private $__columns = [];
    private $__rowParms = ['table'=>null,
                           'where'=>null,
                           'sql'=>null,
                           'parms'=>null,
                           'id'=>null
                          ];

    /**
     * Constructor
     *
     * @param string $sql   [description]
     * @param array  $parms [description]
     */
    function __construct($sql, $parms)
    {
        $this->__rowParms['sql'] = $sql;
        $this->__rowParms['parms'] = $parms;

        foreach ($this as $n => $v) {
            if ($n == '__rowParms' || $n == '__columns') {
                continue;
            }
            $this->__columns[$n] = $v;
            unset($this->{$n});
        }
    }


    /**
     * Save data
     *
     * @return bool Salva os dados no banco de dados [insert/update]
     */
    function save()
    {
        //if($this->id == null) //INSERT INTO
        //else //UPDATE

        /* ex.: INSERT INTO ($this->__table) SET ($this->$key) = ($this->$value)
         *      UPDATE FROM ($this->__table) VALUES(($this->$key) = ($this->$value)) WHARE ($this->__where)
         *
         *      in foreach: bypass $__table and $__whare !!
         */
    }


    /**
     * Get parameter value
     *
     * @param string $parm name of param
     *
     * @return bool|array    False or array data
     */
    function get($parm = false)
    {
        if (isset($this->__columns[$parm])) {
            return $this->__columns[$parm];
        }
        return false;
    }

    /**
     * First row
     *
     * @return array [description]
     */
    function first()
    {
        return reset($this->__columns);
    }

    /**
     * Next row
     *
     * @return array [description]
     */
    function next()
    {
        return next($this->__columns);
    }

    /**
     * Get all data row
     *
     * @return array return all data
     */
    function getAll()
    {
        foreach ($this->__columns as $k => $v) {
            $a[$k] = $v;
        }
        return $a;
    }

    /**
     * Set parameter
     *
     * @param string|array $parm  Name of parameter or array of parameter name and value
     * @param mixed        $value Value of parameter
     *
     * @return boolean
     */
    function set($parm, $value = null)
    {
        if (is_array($parm)) {
            foreach ($parm as $k => $v) {
                $this->__columns[$k] = $v;
            }
            return $this;
        } elseif (isset($this->__columns[$parm])) {
            $this->__columns[$parm] = $value;
            return $this;
        } else {
            return false;
        }
    }
}
