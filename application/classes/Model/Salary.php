<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Salary extends ORM {
    protected $_table_name = 'salaries';
	
	protected $_belongs_to = array(
		'user_from'  => array(
			'model'       => 'User',
			'foreign_key' => 'from_id',
		),
		'user_to'  => array(
			'model'       => 'User',
			'foreign_key' => 'to_id',
		),
    );
	
	public function get_salary_manager_id($user_id) {
		$salaries = $this->where('from_id', '=', $user_id)->find_all()->as_array();
		return $salaries;
	}
}