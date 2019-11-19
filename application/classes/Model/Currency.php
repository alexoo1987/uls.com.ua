<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Currency extends ORM {
    protected $_table_name = 'currencies';

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
			),
			'ratio' => array(
				array('not_empty'),
				array('numeric'),
			)
		);
	}
	
	public function get_by_code($code) {
		return $this->where('code', '=', $code)->find();
	}
}