<?php defined('SYSPATH') or die('No direct script access.');

class Model_ManagerSalary extends ORM {
    protected $_table_name = 'manager_salary';

    protected $_belongs_to = array(
        'manager'  => array(
            'model'       => 'User',
            'foreign_key' => 'manager_id',
        ),
    );
}