<?php
namespace Feather\Table;
#########################################################################
# File Name: AbstractTable.php
# Desc: 
# Author: liufeng
# mail: liufeng1@yongche.com
# Created Time: 2014年09月25日 星期四 15时11分36秒
#########################################################################

abstract class AbstractTable {

    /**
     * default configuration
     * is_persistent标识是否是长连接
     */
    public static $defaultConfig = array(
        'host'          => '', 
        'port'          => 3306,
        'username'      => '', 
        'password'      => '', 
        'dbname'        => '', 
        'charset'       => 'utf8',
        'is_persistent' => false,
    );  

    protected $_config = array();

    //db connection
    protected $_connection = null;

    //final sql
    protected $_query;

    protected $_where = array();

    protected $_orderBy = array(); 

    protected $_groupBy = array(); 

    protected $_params = array();

    protected $_tableName = null;

    protected $_primary = null;

    protected $_error;

    public $count = 0;

    /**
     * 创建数据库连接实例 
     *
     * @param array $config Database config
     */
    public function __construct($config) {
        $this->_config = array_merge(self::$defaultConfig, $config);
        $this->connect();
    }

    public function __toString() {
        return md5(serialize($this->_config));
    }

    /**
     * 关闭数据库连接 
     */
    public function __destruct(){
        if ($this->_connection){
            $this->_connection = null;
        }
    }

    /**
     * 域置为空 
     */
    protected function reset(){
        $this->_where = array();
        $this->_orderBy = array();
        $this->_groupBy = array(); 
        $this->_bindParams = array();
        $this->_query = null;
        $this->count = 0;
    }

    /**
     * 允许多次操作的 ORDER BY 操作.
     *
     * 示例 $db->orderBy('id','desc')->orderBy('name','asc');
     *
     * @param string $field 数据库表的列名.
     * @param string $direction 排序方式.
     *
     * @return this 
     */
    public function orderBy($field, $direction = self::TYPE_DESC){
        $allowedDirection = Array ("ASC", "DESC");
        $direction = strtoupper (trim ($direction));

        if (empty($direction) || !in_array ($direction, $allowedDirection)){
            die ('Wrong order direction: '.$direction);
        }
        $this->_orderBy[$field] = $direction;
        return $this;
    } 

    /**
     * GROUP BY 操作.
     *
     * 示例 $MySqliDb->groupBy('id');
     *
     * @param string $groupByField 数据库表的列名.
     *
     * @return this 
     */
    public function groupBy($groupByField){
        $this->_groupBy[] = $groupByField;
        return $this;
    } 
    /**
     * 允许多次拼接where操作.与操作。
     *
     * 示例 $db->where('id', 7)->where('title', 'MyTitle');
     *
     * @param string $whereProp 数据库表的列名 
     * @param mixed  $whereValue 数据库列的值
     * @param string $operator 操作方式
     *        例如  ‘between’,'in'
     *
     * @return this 
     */
    public function where($whereProp, $whereValue = null, $operator = null){
        if ($operator){
            $whereValue = Array ($operator => $whereValue);
        }
        $this->_where[] = Array ("AND", $whereValue, $whereProp);
        return $this;
    }

    /**
     * 允许多次拼接where操作.或操作。
     *
     * 示例 $db->orWhere('id', 7)->where('title', 'MyTitle');
     *
     * @param string $whereProp 数据库表的列名 
     * @param mixed  $whereValue 数据库列的值
     * @param string $operator 操作方式
     *        例如  ‘between’,'in'
     *
     * @return this 
     */
    public function orWhere($whereProp, $whereValue = null, $operator = null){
        if ($operator)
            $whereValue = Array ($operator => $whereValue);

        $this->_where[] = Array ("OR", $whereValue, $whereProp);
        return $this;
    }

    /**
     * insert和update操作的sql拼装函数。
     */
    protected function _buildTableData ($tableData){
        if (!is_array ($tableData)){
            return;
        }
        $isInsert = strpos ($this->_query, 'INSERT');
        $isUpdate = strpos ($this->_query, 'UPDATE');

        if ($isInsert !== false) {
            $this->_query .= '(`' . implode(array_keys($tableData), '`, `') . '`)';
            $this->_query .= ' VALUES(';
        }

        foreach ($tableData as $column => $value) {
            if ($isUpdate !== false)
                $this->_query .= "`" . $column . "` = ";

            if (!is_array ($value)) {
                $this->_bindParam ($value);
                $this->_query .= '?, ';
                continue;
            }
        }
        $this->_query = rtrim($this->_query, ', ');
        if ($isInsert !== false){
            $this->_query .= ')';
        }
    }

    protected function _buildWhere (){
        if (empty ($this->_where)){
            return;
        }
        $this->_query .= ' WHERE ';

        // 干掉第一个 AND/OR
        $this->_where[0][0] = '';
        foreach ($this->_where as $cond) {
            list ($concat, $wValue, $wKey) = $cond;

            $this->_query .= " " . $concat ." " . $wKey;

            if ($wValue === null){
                continue;
            }
            if (!is_array ($wValue)){
                $wValue = Array ('=' => $wValue);
            }
            $key = key ($wValue);
            $val = $wValue[$key];
            switch (strtolower ($key)) {
                case 'not in':
                case 'in':
                    $comparison = ' ' . $key . ' (';
                    if (is_object ($val)) {
                        $comparison .= $this->_buildPair ("", $val);
                    } else {
                        foreach ($val as $v) {
                            $comparison .= ' ?,';
                            $this->_bindParam ($v);
                        }
                    }
                    $this->_query .= rtrim($comparison, ',').' ) ';
                    break;
                case 'not between':
                case 'between':
                    $this->_query .= " $key ? AND ? ";
                    $this->_bindParams ($val);
                    break;
                default:
                    $this->_query .= $this->_buildPair ($key, $val);
            }
        }
    }

    protected function _buildGroupBy(){
        if (empty ($this->_groupBy)){
            return;
        }

        $this->_query .= " GROUP BY ";
        foreach ($this->_groupBy as $key => $value)
            $this->_query .= $value . ", ";

        $this->_query = rtrim($this->_query, ', ') . " ";
    }

    protected function _buildOrderBy(){
        if (empty ($this->_orderBy)){
            return;
        }
        $this->_query .= " ORDER BY ";
        foreach ($this->_orderBy as $prop => $value)
            $this->_query .= $prop . " " . $value . ", ";

        $this->_query = rtrim ($this->_query, ', ') . " ";
    }

    protected function _buildLimit($numRows){
        if (!isset ($numRows)){
            return;
        }
        if (is_array ($numRows))
            $this->_query .= ' LIMIT ' . (int)$numRows[0] . ', ' . (int)$numRows[1];
        else
            $this->_query .= ' LIMIT ' . (int)$numRows;
    }

    abstract public function query($sql);

    abstract public function get($numRows = null, $columns = '*');

    abstract public function getOne($columns = '*');

    abstract public function insert($insertData);

    abstract public function update($tableData);

    abstract public function delete( $numRows = null);

    abstract public function beginTransaction();

    abstract public function commit();

    abstract public function rollback();

    abstract protected function _throwDbException();

    abstract public function getLastError();
} // END class
