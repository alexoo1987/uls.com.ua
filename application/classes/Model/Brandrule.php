<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Brandrule extends ORM {
    protected $_table_name = 'brandrules';
	
	
	protected $_belongs_to = array(
		'brand'  => array(
			'model'       => 'Brand',
			'foreign_key' => 'brand_id',
		),
    );
}