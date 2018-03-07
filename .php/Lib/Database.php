<?php
/**
 * Lib\Database
 * PHP version 7
 *
 * @category  Database
 * @package   Library
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      Site <https://phatto.ga>
 */
namespace Lib;
use PDO;

/**
 * Database Class
 *
 * @category Database
 * @package  Library
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga>
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


    public function load($table, $where = null)
    {
        if($where === null) $where = "";

        $sql = "SHOW COLUMNS FROM $table";

        $sth = $this->connect()->prepare($sql);
        $sth->execute();
        $this->rows = $sth->rowCount();
        $this->error['load'] = $sth->errorInfo();

        if ($sth->columnCount() > 0) {

            $row = new DbRow($table);
            $this->result = $sth->fetchAll(PDO::FETCH_FUNC, function() use ($row){
                $arg = func_get_args();

                echo '<br>private $'.$arg[0].'; //'.$arg[1].', '.$arg[2].', '.$arg[3].', '.$arg[4].', '.$arg[5];
                $row->{$arg[0]} = null;
                $row->setParamData($arg[0], ['type'=>$arg[1],'null'=>($arg[2]=='NO'?false:true),'key'=>$arg[3], 'default'=>$arg[4],'arg'=>$arg['5']]);
                
            });

            return $row;
        }

        return false;
    }
}

/**
 * Row Class
 *
 * @category Database
 * @package  Data
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga>
 */
class Row
{
    private $___query = '';
    private $___param = [];

    /**
     * Constructor
     *
     * @param string $sql   [description]
     * @param array  $parms [description]
     */
    function __construct($sql, $param)
    {
        $this->___query = $sql;
        $this->___param = $param;
    }

    /**
     * Get parameter value
     *
     * @param string $parm name of param
     *
     * @return bool|array    False or array data
     */
    function get($field = false)
    {
        return isset($this->$field) ?$this->$field : false;
    }

 
     /**
     * Get all data row
     *
     * @return array return all data
     */
    function getAll()
    {
        foreach ($this as $key => $value) {
            if($key == '___query' || $key == '___param') continue;
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Set parameter
     *
     * @param string|array $parm  Name of parameter or array of parameter name and value
     * @param mixed        $value Value of parameter
     *
     * @return boolean
     */
    function set($field, $value = null)
    {
        if (is_array($field)) {
            foreach ($field as $key => $value) {
                if(isset($this->$key)) $this->$key = $value;
            }
            return $this;
        } elseif (isset($this->$field)) {
            $this->$field = $value;
            return $this;
        } else {
            return false;
        }
    }
}


/**
 * New Row for Model builder
 *
 * @category Database
 * @package  Data
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga>
 */
class DbRow
{
    public $id = null;
    private $___where = '';
    private $___table = '';
    private $___param = [];
    private $___error = '';


    function __construct($table = null)
    {
        $this->___table = $table;
    }

    public function getParamData($name)
    {
        return isset($this->___param[$name]) ? $this->___param[$name] : false;
    }

    public function setParamData($name, $data)
    {
        $this->___param[$name] = $data;
        return $this;
    }

    public function get($field)
    {
        if(isset($this->$field)) return $this->$field;
        return false;
    }

    public function set($field, $value)
    {
        $this->$field = $value;
        return $this; 
    }

    public function where($where = '')
    {
        $this->$___where = $where;
        return $this;
    }

    // Salva os dados corrntes no banco - insert|update
    public function save($id = false)
    {
        if($id !== false) $this->id = 0 + $id;

        $action = $this->id === null ? 'insert' :'update';        
        return $this->doSave($action);
    }

    // Softdelete (default: muda o campo status para 3)
    // ou apaga o registro corrente do banco de dados.
    public function delete($soft = true)
    {
        $ret = $this->doDelete($soft, 3);
        if($soft === false) $this->clear();
        return $ret;
    }

    // UnDelete o registro corrente (muda o campo status para 1)
    public function unDelete()
    {
        return $this->doDelete(true, 1);
    }


    //Carrega os dados para o indice $ID
    public function find($id, $status = null)
    {
        $sql = 'SELECT * FROM '.$this->___table.' WHERE id=:id '.($status !== null ? 'AND status='.(0+$status) : '').' '.$this->___where;

        $db = new DataBase;
        $result = $db->query($sql, [':id'=>$id]);

        if(isset($result[0])){
            foreach($this as $key=>$value){
                if($key == '___where' || $key == '___table' || $key == '___param' || $key == '___error') continue;
                $this->$key = $result[0]->get($key);
            }
        } else {
            $this->___error = "Can't find index $id in ".$this->___table;
            return false;
        }

        return $this;
    }

    // PEga todos os registros e retorna uma coleção dessa classe...
    // TODO . . . provavelmente será passada para um MODEL base
    public function findAll($where = null)
    {
        //TODO construir ...
        echo 'TODO: a construir...';
    }


    // ---------------------- privates ----------------------    

    //Apaga todos os dados menos o nome da tabela
    private function clear()
    {
        foreach($this as $key=>$value){
            if($key == '___table') continue;
            $this->$key = null;
        }
        return $this;
    }

    private function doDelete($soft, $status = 3)
    {
        $sql = $soft === false 
                ? 'DELETE FROM '.$this->___table.' WHERE id='.$this->id.' '.$this->___where
                : 'UPDATE '.$this->___table.' SET status='.$status.' WHERE id='.$this->id.' '.$this->___where;

        echo '<br>
        Sql: '.$sql.'

        ';

        $db = new Database;
        $db->query($sql);

        $this->status = 3;
        return $db->getRows();
    }

    private function getParams()
    {
        $set = 'SET ';
        foreach($this as $key=>$value){
            if($key == '___where' || $key == '___table' || $key == '___param' || $key == '___error') continue;

            $set .= '`'.$key.'`=:'.$key.',';
            $pet[':'.$key] = $value;
        }
        $set = substr($set, 0, -1);
        return ['set'=>$set, 'param'=>$pet];
    }


    private function doSave($action = 'insert')
    {
        //mount SETs string
        $set = ' SET ';
        foreach($this as $key=>$value){
            if($key == 'id' || $key == '___where' || $key == '___table' || $key == '___param' || $key == '___error') continue;
            if($action == 'update' && trim($this->___param[$key]['key']) !== '') continue;
            if($this->___param[$key]['type'] == 'timestamp' && trim($value) =='') continue;
            $set .= '`'.$key.'`=:'.$key.',';
        }
        $set = substr($set, 0, -1).' ';

        //set by "action" (insert|update)
        if($action == 'insert'){
            $sql = "SELECT AUTO_INCREMENT as id FROM information_schema.tables WHERE  table_name='".$this->___table."';";
            $sql .= 'INSERT INTO '.$this->___table.' '.$set.' '.$this->___where; 
        } else {
            $this->___where = ' WHERE `id`='.$this->id;
            $sql = 'UPDATE '.$this->___table.$set.$this->___where;
        }

        //Prepare ...
        $db = new Database;
        $sth = $db->connect()->prepare($sql);

        //set values
        foreach($this as $key=>$value){
            if($key == 'id' || $key == '___where' || $key == '___table' || $key == '___param' || $key == '___error') continue;
            if($action == 'update' && trim($this->___param[$key]['key']) !== '') continue;

            $type = (substr($this->___param[$key]['type'], 0, 3) == 'int') ? PDO::PARAM_INT : PDO::PARAM_STR;
            if($this->___param[$key]['type'] == 'timestamp' && trim($value) =='') continue;
            $sth->bindValue(':'.$key, $value, $type);
        }

        //executing
        $sth->execute();

        if($action == 'update') return $this;

        //INSERT, ONLY
        $result = $sth->fetch();

        if(isset($result['id'])){
            $this->id = $result['id'];
            return $this;
        } else {
            $this->___error = 'Error in '.$action.' ... ';
            return false;
        }
    }
}
