<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_OrderEpartsTm extends ORM {
    protected $_table_name = 'order_eparts_tm';
    public $salary_arr = array();
	
	
	protected $_belongs_to = array(
		'order'  => array(
			'model'       => 'Order',
			'foreign_key' => 'order_id',
		),
//        'state'  => array(
//            'model'       => 'State',
//            'foreign_key' => 'state_id',
//        ),
//        'user'  => array(
//            'model'       => 'User',
//            'foreign_key' => 'user_id',
//        ),
    );
}