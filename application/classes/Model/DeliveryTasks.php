<?php defined('SYSPATH') or die('No direct script access.');

class Model_Penalty extends ORM {
    protected $_table_name = 'delivery_tasks';

    protected $_belongs_to = array(
        'user'  => array(
            'model'       => 'User',
            'foreign_key' => 'user_id',
        )
    );
}