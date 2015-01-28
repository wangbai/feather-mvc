<?php
namespace Feather\Table;
#########################################################################
# File Name: Mysqli.php
# Desc:基于Mysqli的常用功能封装。 
# Author: liufeng
# mail: liufeng1@yongche.com
# Created Time: 2014年12月08日 星期一 15时30分24秒
#########################################################################

class Mysqli extends AbstractTable {

    /**
     * 创建mysqli实例 
     */
    public function connect(){
        if (!empty($this->_connection)) {
            return;
        }   

        if (!extension_loaded('mysqli')){
            throw new Exception('No mysqli extension installed');
        }

        $config = $this->_config;
        $host = $config['host'];
        $port = $config['port'];
        $user = $config['username'];
        $password = $config['password'];
        $database = $config['dbname'];
        $charset = $config['charset'];

        $mysqli = new \mysqli ($host, $user, $password, $database, $port);

        if (!$mysqli) {
            $this->_throwDbException();
        }

        $mysqli->set_charset($charset);

        $this->_connection = $mysqli;

        return;
    }

    /**
     *
     * 执行标准sql，返回一个array对象 
     * 示例:$db->query('SELECT name, color, calories FROM fruit ORDER BY name');
     *
     * @param string $sql
     * @return array 
     */
    public function query($sql) {
        
        $this->_query = filter_var($sql,FILTER_SANITIZE_STRING);
        $stmt = $this->_buildQuery();
        
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->reset();

        return $this->_genResult($stmt);
    }

    /**
     * select * 操作的简单封装 
     *
     * @param $numRows   分页参数
     *        like array(2,10),相当于limit 2,10
     * @param $columns 返回数据列，不传参数为获取所有列
     *        like array('name','age') 或 “name，age”
     *
     * @return array 
     */
    public function get($numRows = null, $columns = '*'){

        if (empty ($columns)){
            $columns = '*';
        }
        $column = is_array($columns) ? implode(', ', $columns) : $columns; 
        $this->_query = "SELECT $column FROM "  . $this->_tableName;
        $stmt = $this->_buildQuery($numRows);
        
        $stmt->execute();
        $this->_error = $stmt->error;
        $this->reset();

        return $this->_genResult($stmt);
    }

    /**
     * 对select * 操作的简单封装,查询一条记录 
     *
     * @param string  $columns 返回数据列，不传参数为获取所有列
     *        like array('name','age') 或 “name，age”
     *
     */
    public function getOne($columns = '*'){
        $res = $this->get (1, $columns);

        if (is_object($res))
            return $res;

        if (isset($res[0]))
            return $res[0];

        return null;
    }

    /**
     * 插入查询操作 
     *
     * @param array $insertData 插入数据库的数据.
     *
     * @return 操作影响行数>0 返回主键值 ，<0 返回false.
     */
    public function insert($insertData){

        $this->_query = "INSERT into " . $this->_tableName;
        $sth = $this->_buildQuery(null, $insertData);
        $sth->execute();
        $this->_error = $sth->error;
        $this->reset();

        return $sth->affected_rows>0?$this->_connection->insert_id:false;
    }

    /**
     * 更新查询操作,需要先执行where()方法
     *
     * @param array $insertData 更新数据库的数据.
     *
     * @return boolean 是否更新成功
     */
    public function update($tableData){

        $this->_query = "UPDATE " . $this->_tableName ." SET ";

        $sth = $this->_buildQuery (null, $tableData);
        $status = $sth->execute();
        $this->reset();
        $this->_error = $sth->error;
        $this->count = $sth->affected_rows;

        return $status;
    }

    /**
     * 删除查询操作,需要先执行where()方法
     *
     * @param array $numRows 删除几条.
     *
     * @return boolean success. true or false.
     */
    public function delete( $numRows = null){

        $this->_query = "DELETE FROM " . $this->_tableName;

        $sth = $this->_buildQuery($numRows);
        $sth->execute();
        $this->_error = $sth->error;
        $this->reset();

        return $sth->affected_rows ;
    }

    public function getLastError(){
        return $this->_error . " " . var_export($this->_connection->error,true);
    }

    /**
     * 启动事务
     */
    public function beginTransaction() {
        $ret = $this->_connection->autocommit(false);
        if (!$ret) {
            throw new Exception('Begin transaction failed');       
        }
    }

    /**
     * 提交事务
     */
    public function commit() {
        $ret = $this->_connection->commit();
        if (!$ret) {
            throw new Exception('Transaction commit failed');
        }
        $this->_connection->autocommit(true);
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        $ret = $this->_connection->rollBack();
        if (!$ret) {
            throw new Exception('Transaction rollback failed');
        }
        $this->_connection->autocommit(true);
    }

    /**
     * 生成sql语句，并预加载。
     *
     * @param int   $numRows 分页 
     * @param array $tableData 返回的数据项
     *
     * @return PDO Returns the $sth object.
     */
    protected function _buildQuery($numRows = null, $tableData = null){

        $this->_buildTableData ($tableData);
        $this->_buildWhere();
        $this->_buildGroupBy();
        $this->_buildOrderBy();
        $this->_buildLimit ($numRows);

        // Prepare query
        $sth = $this->_prepareQuery();

        // Bind parameters to statement if any
        if (count ($this->_params) > 0){
            call_user_func_array(array($sth, 'bind_param'), $this->refValues($this->_params));
        }
        return $sth;
    }

    /**
     * 生成预处理PDOStatement
     *
     * @return PDOStatement 
     */
    protected function _prepareQuery(){
        if (!$stmt = $this->_connection->prepare($this->_query)) {
            trigger_error("Problem preparing query ($this->_query) " . $this->_connection->error, E_USER_ERROR);
        }
        return $stmt;
    }


    /**
     * 绑定参数到域_bindParams中，_bindParams[0] 存放属性标识符 
     *
     * @param string Variable value
     */
    protected function _bindParam($value){
        if(!isset($this->_params[0])){
            $this->_params[0] = "";
        }
        $this->_params[0] .= $this->_determineType($value);
        array_push ($this->_params, $value);
    }

    /**
     * @param Array Variable with values
     */
    protected function _bindParams ($values){
        foreach ($values as $value)
            $this->_bindParam ($value);
    }

    /**
     * Helper function to add variables into bind parameters array and will return
     * its SQL part of the query according to operator in ' $operator ?' or
     * ' $operator ($subquery) ' formats
     *
     * @param Array Variable with values
     */
    protected function _buildPair ($operator, $value){
        if (!is_object($value)) {
            $this->_bindParam($value);
            return ' ' . $operator. ' ? ';
        }

        return ;
    }

    protected function _throwDbException() {
        if ($this->_connection) {
            throw new Exception($this->_connection->error,
                $this->_connection->errorno);
        } else {
            throw new Exception(" mysql connection is error",
                10086);
        }
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    protected function refValues(& $arr){
        if (strnatcmp(phpversion(), '5.3') >= 0) {                                                          
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[] = & $arr[$key];
            }        
            return $refs;
        }        
        return $arr;
    }        

    protected function _determineType($item){
        switch (gettype($item)){
            case 'NULL':
            case 'string':
                return 's';
                break;
            case 'boolean':
            case 'integer':
                return 'i';
                break;
            case 'blob':
                return 'b';
                break;
            case 'double':
                return 'd';
                break;
        }
        return '';
    }

    private function _genResult(\mysqli_stmt $stmt){
        
        $meta = $stmt->result_metadata();
        $results = array();
        $row = array();
        while ($field = $meta->fetch_field()) {
            $row[$field->name] = null;
        }    
        call_user_func_array(array($stmt, 'bind_result'), self::refValues($row));

        while ($stmt->fetch()) {
            $x = array();
            foreach ($row as $key => $val) {
                $x[$key] = $val;
            }
            $this->count++;
            array_push($results, $row); 
        }    

        return $results;
    }
} // END class
