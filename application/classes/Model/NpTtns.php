<?php defined('SYSPATH') or die('No direct script access.');

class Model_NpTtns extends ORM {
    protected $_table_name = 'np_ttns';
    public $salary_arr = array();

    protected $_belongs_to = array(
        'area'  => array(
            'model'       => 'Orders',
            'foreign_key' => 'order_id',
        ),
    );

}