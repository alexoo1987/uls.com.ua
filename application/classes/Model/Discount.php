<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Discount extends ORM {
    protected $_table_name = 'discounts';
	
	protected $_has_many = array(
		'discount_limits' => array('model' => 'DiscountLimit','foreign_key' => 'discount_id'),
	);
	
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
			)
		);
	}
	
	public static function getStandart() {
		$standart_discount = ORM::factory('Discount')->where('standart', '=', 1)->find();
		if($standart_discount->id) {
			return $standart_discount;
		} else {
			return ORM::factory('Discount')->where('id', '=', 1)->find();
		}
	}

    public static function getClient_standart() {
        $standart_discount = ORM::factory('Discount')->where('standart', '=', 1)->find();
        if($standart_discount->id) {
            return $standart_discount;
        } else {
            return ORM::factory('Discount')->where('id', '=', 1)->find();
        }
    }
	
	public static function getStandartId() {
		$standart_discount = self::getStandart();
		if($standart_discount) {
			return $standart_discount->id;
		} else {
			return 1;
		}
	}
}