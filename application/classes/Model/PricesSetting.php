<?php defined('SYSPATH') or die('No direct script access.');

class Model_PricesSetting extends ORM {
	protected $_table_name = 'prices_setting';

	protected $_belongs_to = array(
		'Supplier'  => array(
			'model'       => 'Supplier',
			'foreign_key' => 'supplier_id',
		)
	);

	public function rules()
	{
		return array(
		);
	}

	public function log($text)
	{
		$this->upload_info .= $text . "\n";
		$this->save();
	}

	public function clear_log()
	{
		$this->upload_info = '';
		$this->save();
	}
}