<?php defined('SYSPATH') or die('No direct script access.');

class Model_CashMovement extends ORM {
	protected $_table_name = 'cash_movement';

	protected $_belongs_to = array(
		'from_user'  => array(
			'model'       => 'User',
			'foreign_key' => 'from_user',
		),
		'to_user'  => array(
			'model'       => 'User',
			'foreign_key' => 'to_user',
		),
	);

	public function rules()
	{
		return array(
		);
	}
}