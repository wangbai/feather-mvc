<?php

namespace Feather\Model;

use Feather\Db\AbstractAdapter;

class Person extends AbstractModel {

    protected $_tableName = 'person';
    protected $_primaryKey = 'id';

    public function __construct(AbstractAdapter $adapter) {
        parent::__construct($adapter);
    }

}// END OF CLASS
