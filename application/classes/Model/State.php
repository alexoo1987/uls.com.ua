<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_State extends ORM {
    protected $_table_name = 'states';

    CONST ORDER_ACCEPT = 1;

	protected $_belongs_to = array(
    );
	
	public function get_state_by_text_id($text_id) {
		return $this->where('text_id', '=', $text_id)->find();
	}
}