<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_ClientPayment extends ORM {
    protected $_table_name = 'client_payments';
	
	protected $_belongs_to = array(
		'client'  => array(
			'model'       => 'Client',
			'foreign_key' => 'client_id',
		),
		'order'  => array(
			'model'       => 'Order',
			'foreign_key' => 'order_id',
		),
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
    );
}