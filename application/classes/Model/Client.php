<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Client extends ORM {

	const TYPE_FIZ = 0;
	const TYPE_JUR = 1;

    protected $_table_name = 'clients';
	
	protected $_belongs_to = array(
		'manager'  => array(
			'model'       => 'User',
			'foreign_key' => 'manager_id',
		),
		'discount'  => array(
			'model'       => 'Discount',
			'foreign_key' => 'discount_id',
		),
		'delivery_method'  => array(
			'model'       => 'DeliveryMethod',
			'foreign_key' => 'delivery_method_id',
		),
    );
	
	protected $_has_many = array(
		'cars'  => array(
			'model'       => 'Car',
			'foreign_key' => 'client_id',
		),
		'documents'  => array(
			'model'       => 'ClientDocument',
			'foreign_key' => 'client_id',
		),
    );
	
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
			),
			'surname' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
			),
			'phone' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
				array(array($this, 'unique'), array('phone', ':value')),
			),

		);
	}
	
	public function filters()
	{
		return array(
			'password' => array(
				array(array(Auth::instance(), 'hash'))
			)
		);
	}
	
	public function unique_key_exists($value, $field = NULL)
	{
		if ($field === NULL)
		{
			// Automatically determine field by looking at the value
			//$field = $this->unique_key($value);
		}

		return (bool) DB::select(array(DB::expr('COUNT(*)'), 'total_count'))
			->from($this->_table_name)
			->where($field, '=', $value)
			->where($this->_primary_key, '!=', $this->pk())
			->execute($this->_db)
			->get('total_count');
	}
	
	public static function get_password_validation($values)
	{
		return Validation::factory($values)
			->rule('password', 'min_length', array(':value', 3))
			->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
	}
	
	public function create_user($values, $expected)
	{
		// Validation for passwords
		$extra_validation = Model_Client::get_password_validation($values);

		return $this->values($values, $expected)->create($extra_validation);
	}
	
	public function update_user($values, $expected = NULL)
	{
		if (empty($values['password']))
		{
			unset($values['password'], $values['password_confirm']);
		}

		// Validation for passwords
		$extra_validation = Model_Client::get_password_validation($values);

		return $this->values($values, $expected)->update($extra_validation);
	}
	
	
	public function login($phone, $password)
	{
		if ( ! is_object($phone))
		{
			$phone = $phone;

			// Load the user
			$client = ORM::factory('Client');
			$client->where('phone', '=', $phone)->find();
		}

		if (is_string($password))
		{
			// Create a hashed password
			$password = Auth::instance()->hash($password);
		}

		// If the passwords match, perform a login
		if ($client->password === $password)
		{

			// Finish the login
			$this->complete_login($client);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}

	public function social_network_login($client_id)
	{
			$client = ORM::factory('Client');
			$client->where('id', '=', $client_id)->find();

			if(!$client->id) {
				return false;
			}else{
				$this->complete_login($client);
				return true;
			}
	}


	protected function complete_login($client)
	{
		$session = Session::instance();
		// Regenerate session_id
		$session->regenerate();
	 
		// Store username in session
		$session->set('current_client', $client);
	 
		return TRUE;
	}
	
	public function logged_in()
	{
		return FALSE !== $this->get_client();
	}
	
	public function get_client()
	{
		$session = Session::instance();
		return $session->get('current_client', FALSE);
	}
	
	
	public function logout($destroy = FALSE, $logout_all = FALSE)
	{
		$session = Session::instance();
		if ($destroy === TRUE)
		{
			// Destroy the session completely
			$session->destroy();
		}
		else
		{
			// Remove the user from the session
			$session->delete('current_client');
	 
			// Regenerate session_id
			$session->regenerate();
		}
	 
		// Double check
		return ! $this->logged_in();
	}
	
	public function get_user_balance() {
		$order_details = array();
			
		$order_details['all_sale'] = 0;
		$order_details['all_in'] = 0;
		$order_details['active_sale'] = 0;
		$order_details['balance'] = 0;
		$order_details['active_balance'] = 0;
		$order_details['debt'] = 0;
		
		$order_disallow = Model_Services::disableStates;

		
		$orders_of_user = ORM::factory('Order')->where('client_id', '=', $this->id)
			->find_all()->as_array();
			
		$client_payments = ORM::factory('ClientPayment')->where('client_id', '=', $this->id)->order_by('date_time')->find_all()->as_array();
		
		foreach($client_payments as $cp) {
			$order_details['all_in'] += $cp->value;
		}
		$order_details['balance'] = $order_details['all_in'];
		
		foreach($orders_of_user as $order) {
			
			foreach($order->orderitems->find_all()->as_array() as $oi) {
				if(in_array($oi->state_id, $order_disallow)) continue;
				$order_details['all_sale'] += $oi->sale_per_unit*$oi->amount;
				
				if($oi->order->archive == 1) {
					$order_details['balance'] -= $oi->sale_per_unit*$oi->amount;
				} else {
					$order_details['active_sale'] += $oi->sale_per_unit*$oi->amount;
				}
			}
		}
		
		$order_details['debt'] = $order_details['all_sale'] - $order_details['all_in'];
		if($order_details['debt'] < 0) $order_details['debt'] = 0;
		$order_details['active_balance'] = $order_details['balance'] - $order_details['active_sale'];
		
		return $order_details;
	}

	public function get_client_type()
	{
		if (is_null($this->client_type)) {
			return '';
		}
		return $this->client_type == self::TYPE_FIZ ? 'Физическое лицо' : 'Юридическое лицо';
	}
}