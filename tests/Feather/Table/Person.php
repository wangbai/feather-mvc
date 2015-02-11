<?php

namespace Feather\Table;

class Person extends \Feather\Table\Mysqli{

    protected $_tableName = "person";
    public function __construct($config) {
        parent::__construct($config);
    }

}// END OF CLASS
