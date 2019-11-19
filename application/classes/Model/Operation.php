<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Operation extends ORM {
    protected $_table_name = 'operations';
	
	protected $_has_many = array(
		'parts' => array(
			'model' => 'Part',
			'foreign_key' => 'operation_id'
		),
		'crosses' => array(
			'model' => 'Cross',
			'foreign_key' => 'operation_id'
		),
		'brands' => array(
			'model' => 'Brand',
			'foreign_key' => 'operation_id'
		),
		'priceitems'  => array(
			'model'       => 'Priceitem',
			'foreign_key' => 'operation_id',
		),
		'unmatched'  => array(
			'model'       => 'Unmatched',
			'foreign_key' => 'operation_id',
		),
	);
	
	protected $_belongs_to = array(
		'supplier'  => array(
			'model'       => 'Supplier',
			'foreign_key' => 'supplier_id',
		),
    );
}