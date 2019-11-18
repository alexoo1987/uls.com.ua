<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Menu extends ORM {
    protected $_table_name = 'menus';
	
	protected $_belongs_to = array(
		'page'  => array(
			'model'       => 'Page',
			'foreign_key' => 'page_id',
		),
    );

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
			),
			'identifier' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
			),
		);
	}
}