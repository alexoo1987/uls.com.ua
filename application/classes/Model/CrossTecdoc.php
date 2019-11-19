<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_CrossTecdoc extends ORM {
    protected $_table_name = 'crosses_td';
    protected $_db_group = 'tecdoc_new';
	
	protected $_belongs_to = array(
		'from_part'  => array(
			'model'       => 'Part',
			'foreign_key' => 'from_id',
		),
		'to_part'  => array(
			'model'       => 'Part',
			'foreign_key' => 'to_id',
		),
    );

	public function rules()
	{
		return array(
		);
	}
}