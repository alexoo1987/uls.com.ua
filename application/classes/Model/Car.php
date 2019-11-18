<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Car extends ORM {
    protected $_table_name = 'cars';
	
	protected $_belongs_to = array(
		'client'  => array(
			'model'       => 'Client',
			'foreign_key' => 'client_id',
		),
    );
}