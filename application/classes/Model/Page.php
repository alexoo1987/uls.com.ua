<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Page extends ORM {
    protected $_table_name = 'pages';
	
	protected $_has_one = array(
    );
	
	protected $_belongs_to = array(
    );

	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
			),
		);
	}
}