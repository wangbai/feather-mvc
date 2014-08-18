<?php

namespace Feather\Db;

abstract class AbstractAdapter {
    
    const FIELD_REPLACER = '?';
 
    public static $defaultConfig = array(
        'host'      => '', 
        'port'      => 3306,
        'username'      => '', 
        'password'  => '', 
        'dbname'  => '', 
        'charset'   => 'utf8'
    );  

    //default configuration for the db connection
    protected $_config = array();

    //db connection
    protected $_connection = null;

    /**
    * Create a Db adapter instance
    *
    * @param array $config Database config
    */
    public function __construct($config) {
        $this->_config = array_merge(self::$defaultConfig, $config);

        $this->connect();
    }

    /**
    * Execute a sql
    *
    * @param string $sql
    * @return mixed 
    */
    public function query($sql) {
        $result = $this->_query($sql);
        if ($result) {
            return $result;
        }

        $this->_throwDbException();
    }

    /**
    * Execute a sql
    *
    * @param string $sql
    * @param array $param
    * @return mixed
    */
    public function secureQuery($sql, $param = array()) {
        $finalSql = "";
        $remain = $sql;
        foreach ($param as $p) {
            $field = $this->escape($p);
            $replacePos = strpos($remain, self::FIELD_REPLACER);

            //no replacer for the param 
            if (empty($replacePos)) {
                break;
            }
            if(is_numeric($p)){
                $finalSql .= substr($remain, 0, $replacePos).$this->escape($p);
            }else{
                $finalSql .= substr($remain, 0, $replacePos)."'".$this->escape($p)."'";
            }
            $remain =  substr($remain, $replacePos + 1);
            if (empty($remain)) {
                break;
            }
        }

        $result = $this->_query($finalSql);
        if ($result !== false) {
            return $result;
        }

        $this->_throwDbException();
    }

    /**
    * Convert to a connection only label
    *
    * @return string
    */
    public function __toString() {
        return md5(serialize($this->_config));
    }

    /**
    * Connect to the DB server
    * 
    * @return
    */
    abstract public function connect();

    /**
    * Release the connection to the DB server
    * After closing, the adapter is destroyed
    *
    * @return bool
    */
    abstract public function close();

    /**
    * Escape the special characters
    *
    * @param string $str
    * @return string
    */ 
    abstract public function escape($str);

    /**
    * Actually send the query to DB server
    *
    * @param $sql input SQL query
    * @return mixed
    */
    abstract protected function _query($sql);

    /**
    * throw the error of the DB server
    */
    abstract protected function _throwDbException();

    /**
    * the number of the affected row
    *
    * @return int
    */
    abstract public function affectedRowsNum();

    /**
    * begin transaction
    *
    * @return
    */
    abstract public function beginTransaction();

    /**
    * commit the transaction
    *
    * @return
    */
    abstract public function commit();

    /**
    * rollback the transaction
    *
    * @return
    */
    abstract public function rollback();

    /*
    * extract sql operation
    * 
    * @param string $sql sql query
    * @return string insert | delete | update | select
    */
    static public function operationExtract($sql) {
        $sql = trim($sql);
        $firstBlank = strpos($sql, ' ');
        $operation = substr($sql, 0, $firstBlank);

        return strtolower($operation);
    }
}// END OF CLASS
