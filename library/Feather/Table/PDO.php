<?php
namespace Feather\Table;
#########################################################################
# File Name: PDO.php
# Desc:基于PDO的常用功能封装。 
# Author: liufeng
# mail: liufeng1@yongche.com
# Created Time: 2014年12月08日 星期一 15时30分24秒
#########################################################################

class PDO extends AbstractTable {

    /**
     * 连接操作,连接是通过创建 PDO 基类的实例而建立的。
     * 如果有任何连接错误，将抛出一个 PDOException 异常对象。
     */
    public function connect(){
        if (!empty($this->_connection)) {
            return;
        }   

        if (!extension_loaded('pdo')) {
            throw new Exception('No pdo extension installed');
        }   

        $config = $this->_config;
        $host = $config['host'];
        $port = $config['port'];
        $user = $config['username'];
        $password = $config['password'];
        $database = $config['dbname'];
        $charset = $config['charset'];
        $isPersistent = $config['is_persistent'];

        $dsn = "mysql:host=$host;dbname=$database;port=$port;charset=$charset";
        $pdo = new \PDO($dsn,$user,$password,array(\PDO::ATTR_PERSISTENT => $isPersistent));
        if (!$pdo) {
            $this->_throwDbException();
        }   

        $this->_connection = $pdo;

        return;
    }

    /**
    * 执行标准sql，返回一个PDOStatement对象 
    * 示例:$db->query('SELECT name, color, calories FROM fruit ORDER BY name');
    *
    * @param string $sql
    * @return PDOStatement 
    */
    public function query($sql) {
        $result = $this->_connection->query($sql);
        if ($result !== false) {
            return $result->fetch(\PDO::FETCH_ASSOC);
        }

        $this->_throwDbException();
    }

    /**
     * 示例： $db->rawQuery('SELECT * from person where id=?', Array (10));
     *
     * @param string $query      带占位符的sql语句 
     * @param array  $bindParams 传入数据 
     * @param bool   $sanitize   true时编码引号 
     *
     * @return array 
     */
    public function rawQuery ($query, $bindParams = null, $sanitize = true){
        $this->_query = $query;
        if ($sanitize){
            $this->_query = filter_var($query, FILTER_SANITIZE_STRING , FILTER_FLAG_NO_ENCODE_QUOTES);
        }
        $sth = $this->_prepareQuery();

        if (is_array($bindParams) === true) {
            $flag = 1;
            foreach ($bindParams as $val) {
                $sth->bindParam($flag,$val);
                $flag++;
            }
        }

        $sth->execute();
        $res = $sth->fetchAll();
        $this->_error = $sth->errorCode();
        $this->reset();

        return $res;
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
        $sth = $this->_buildQuery($numRows);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
        $this->_error = $sth->errorCode();
        $this->reset();

        return $res;
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
        $this->_error = $sth->errorCode();
        $this->reset();

        return $sth->rowCount()>0?$this->_connection->lastInsertId():false;
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
        $this->_error = $sth->errorCode();
        $this->count = $sth->rowCount();

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
        $this->_error = $sth->errorCode();
        $this->reset();

        return ($sth->rowCount() > 0);
    }


    public function getLastError(){
        return $this->_error . " " . var_export($this->_connection->errorInfo(),true);
    }

    /**
     * 启动事务
     */
    public function beginTransaction() {
        $ret = $this->_connection->beginTransaction();
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
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        $ret = $this->_connection->rollBack();
        if (!$ret) {
            throw new Exception('Transaction rollback failed');
        }
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
            $flag = 1;
            foreach($this->_params as $param){
                $sth->bindValue($flag,$param);
                $flag++;
            }
        }
        return $sth;
    }

    /**
     * 生成预处理PDOStatement
     *
     * @return PDOStatement 
     */
    protected function _prepareQuery(){
        if (!$sth = $this->_connection->prepare($this->_query)) {
            throw new Exception("Problem preparing query ($this->_query) " . $this->_connection->errorCode());
        }
        return $sth;
    }


    /**
     * 绑定参数到域_bindParams中，_bindParams[0] 存放属性标识符 
     *
     * @param string Variable value
     */
    protected function _bindParam($value){
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
            $this->_bindParam ($value);
            return ' ' . $operator. ' ? ';
        }

        return ;
    }

    protected function _throwDbException() {
        if ($this->_connection) {
            throw new Exception($this->_connection->errorInfo(),
                $this->_connection->errorCode());
        } else {
            throw new Exception(" mysql connection is error",
                10086);
        }
    }

} // END class
