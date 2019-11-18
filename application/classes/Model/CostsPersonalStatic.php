<?php defined('SYSPATH') or die('No direct script access.');

class Model_CostsPersonalStatic extends ORM {
    protected $_table_name = 'costs_personal_static';

    protected $_belongs_to = array(
        'user'  => array(
            'model'       => 'User',
            'foreign_key' => 'user_id',
        ),

    );
}