<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_OrderitemLog extends ORM {
    protected $_table_name = 'orderitems_log';
    public $salary_arr = array();
	
	
	protected $_belongs_to = array(
		'orderitem'  => array(
			'model'       => 'Orderitem',
			'foreign_key' => 'orderitem_id',
		),
        'state'  => array(
            'model'       => 'State',
            'foreign_key' => 'state_id',
        ),
        'user'  => array(
            'model'       => 'User',
            'foreign_key' => 'user_id',
        ),
    );
}