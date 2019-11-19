<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Priceitem extends ORM {
    protected $_table_name = 'priceitems';
    protected $_db_group = 'tecdoc_new';

	protected $_belongs_to = array(
		'part'  => array(
			'model'       => 'Part',
			'foreign_key' => 'part_id',
		),
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

	private $_price = null;
	private $_price_for_client = null;
	public $weight = null;
    public $suplier_code_tehnomir = null;
    public $volume = null;
    public $return_flag = null;

	public function get_price() {
		if(empty($this->_price)) $this->_price = round($this->price * $this->currency->ratio, 2)/*ceil($this->price * $this->currency->ratio/*)*/;
		return $this->_price;
	}
	
	public function get_price_for_client($client = false, $standart = false, $discount_id = false) {
		if(empty($this->_price_for_client)) {
			$price = $this->get_price();
			
			$discount = $this->get_discount_for_client($client, $standart, $discount_id);
			
			foreach($discount->discount_limits->find_all()->as_array() as $dl) {
				if($price > $dl->from && ($price <= $dl->to || $dl->to == 0)) {
					$price = round(($price * (100 + $dl->percentage) / 100), 0);
					break;
				}
			}
		}
		
		//return $this->_price_for_client;
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
	
	public function get_from_arr($array) {
		$priceitem = ORM::factory('Priceitem');
		$priceitem->set('price', $array['price']);
		$priceitem->set('currency_id', $array['currency_id']);
        $priceitem->suplier_code_tehnomir = $array['supplier_code'];
		$priceitem->set('amount', $array['amount']);
		$priceitem->set('delivery', $array['delivery']);
		$priceitem->set('supplier_id', $array['supplier_id']);
		if($array['part_id'])
			$priceitem->part_id = $array['part_id'];
		else
			$priceitem->part = ORM::factory('Part')->get_part($array['article'], $array['brand'], $array['name']);
			
		return $priceitem;
	}
}