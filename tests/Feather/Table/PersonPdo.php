<?php

namespace Feather\Table;

class PersonPdo extends \Feather\Table\PDO{

    protected $_tableName = "person";
    public function __construct($config) {
        parent::__construct($config);
    }

}// END OF CLASS
