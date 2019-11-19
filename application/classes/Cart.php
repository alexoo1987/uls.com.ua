<?php defined('SYSPATH') or die('No direct script access.');

class Cart {
	protected static $_instance;
	protected $_session;
	protected $_session_key = "user_cart";
	protected $_content = array();
	
	protected function __construct()
	{
		// Load Session object
		$this->_session = Session::instance();

		// Grab the shopping cart array from the session table, if it exists
		if ( ! $this->_content = $this->_session->get($this->_session_key))
		{
			// Cart not exists, set basic values
			$this->_content = array();
		}
	}
	public function __get($key)
	{
		if ($key == 'content')
		{
			return $this->{'_'.$key};
		}
	}
	
	public static function instance()
	{
		// Recreate object if you set new config
		if ( ! self::$_instance)
		{
			self::$_instance = new Cart;
		}
		return self::$_instance;
	}
	
	protected function _save()
	{
		// save in session
		$this->_session->set(
			$this->_session_key,
			$this->_content
		);
		return $this;
	}
	
	public function add($id, $qty = 1, $number = 0) {
		if(isset($this->_content[$id])) {
			$this->_content[$id]['qty'] += $qty;
            //$this->_content[$id]['number'] += $number;
		}
		else {
			$this->_content[$id]['id'] = $id;
			$this->_content[$id]['qty'] = $qty;
            $this->_content[$id]['number'] = $number;
		}
		
		$this->_save();
	}
    public function unadd($id, $qty = 1) {
        if(isset($this->_content[$id])) {
            $this->_content[$id]['qty'] -= $qty;
        }

        $this->_save();
    }
	
	public function get($id) {
		return $this->_content[$id];
	}
	
	public function delete($id = NULL) {
		if (empty($id)) 
		{
			// Delete all
			$this->_content = array();
		}
		else
		{
			unset($this->_content[$id]);
		}
		
		$this->_save();
	}
	
	public function get_count() {
		$result = array(
			'qty' => 0,
            'number' => 0,
			'price' => 0
		);
		
		foreach($this->_content as $item) {
			if(is_numeric($item['id'])) {
				$priceitem = ORM::factory('Priceitem', $item['id']);
			} else {
				$json_array = json_decode(base64_decode(str_replace('_','=',$item['id'])), true);
				$priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
			}
			
			$result['qty'] += $item['qty'];
            $result['number'] += $item['number'];
			$result['price'] += round($priceitem->get_price_for_client() * $item['qty'], 0);
		}
		
		return $result;
	}
}