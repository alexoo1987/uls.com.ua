<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Unmatched extends ORM {
    protected $_table_name = 'unmatched';
	
	protected $_belongs_to = array(
		'supplier'  => array(
			'model'       => 'Supplier',
			'foreign_key' => 'supplier_id',
		),
		'currency'  => array(
			'model'       => 'Currency',
			'foreign_key' => 'currency_id',
		),
    );

	public function rules()
	{
		return array(
		);
	}
	
	public function get_price() {
		return round($this->price * $this->currency->ratio, 2)/*ceil($this->price * $this->currency->ratio/*)*/;
	}
	
	public function get_price_for_client($client = false, $standart = false, $discount_id = false) {
		$price = $this->get_price();
		
		$discount = $this->get_discount_for_client($client, $standart, $discount_id);
		
		foreach($discount->discount_limits->find_all()->as_array() as $dl) {
			if($price > $dl->from && ($price <= $dl->to || $dl->to == 0)) {
				$price = round(($price * (100 + $dl->percentage) / 100), 0);
				break;
			}
		}
		
		return $price;
	}
	
	public function get_discount_for_client($client = false, $standart = false, $discount_id = false) {
		if($discount_id) {
			$discount = ORM::factory('Discount', $discount_id);
		} elseif($client) {
			$discount = $client->discount;
		} else {
			if(!ORM::factory('Client')->logged_in() || $standart) {
				$discount = ORM::factory('Discount')->getStandart();
			} else {
				$discount = ORM::factory('Client')->get_client()->discount;
			}
		}
		
		return $discount;
	}
	
	private $_reasons = array("bad_article" => "Артикул не найден", "bad_brand" => "Бренд не найден", "else" => "Другая причина");
	public function get_reason() {
		return $this->_reasons[$this->reason];
	}
}