<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Catalogfromip extends ORM {
    protected $_table_name = 'catalog_from_ip';

	public function check_ip() {
	    return true; // Comment this line, if need to enable block catalog by IP
		$ip = $_SERVER["REMOTE_ADDR"];

		$ip_obj = ORM::factory('Catalogfromip')->where('client_ip', '=', $ip)->find();
		
		if(!empty($ip_obj->id)) {
			$start_date = strtotime($ip_obj->start_date);
			$delta = round(abs(time() - $start_date) / 60,2);

			if($delta > 60) {
				$ip_obj->last_count = 0;
				$ip_obj->start_date = date('Y-m-d H:i:s', time());
			}

			if($ip_obj->last_count >= 1500 || $ip_obj->banned == '1') return false;
			$ip_obj->total_count++;
			$ip_obj->last_count++;
			$ip_obj->last_date = date('Y-m-d H:i:s', time());

			$ip_obj->save();
		}
		else {
			$ip_obj = ORM::factory('Catalogfromip');
			$ip_obj->client_ip = $ip;
			$ip_obj->total_count = 1;
			$ip_obj->last_count = 1;
			$ip_obj->last_date = date('Y-m-d H:i:s', time());
			$ip_obj->start_date = date('Y-m-d H:i:s', time());
			$ip_obj->save();
		}
		return true;
	}
}
