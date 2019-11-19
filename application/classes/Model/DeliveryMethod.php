<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_DeliveryMethod extends ORM {
    protected $_table_name = 'delivery_methods';

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
			),
			'price' => array(
				array('not_empty'),
				array('digit'),
				array('max_length', array(':value', 64)),
			)
		);
	}
}