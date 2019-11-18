<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Menuitem extends ORM {
    protected $_table_name = 'menu_items';
	
	protected $_belongs_to = array(
		'menu'  => array(
			'model'       => 'Menu',
			'foreign_key' => 'menu_id',
		),
		'page'  => array(
			'model'       => 'Page',
			'foreign_key' => 'page_id',
		),
    );
	
	protected $_has_many = array(
		'menuitems' => array(
			'model' => 'Menuitem',
			'foreign_key' => 'parent_id',
		),
	);

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 64)),
			),
			'link_title' => array(
				array('max_length', array(':value', 255)),
			),
		);
	}
	
	public function getItems($menu_id) {
		return $this->where('menu_id', '=', $menu_id)->and_where('parent_id', 'IS', NULL)->order_by('order_by', 'asc')->find_all()->as_array();
	}
	
	public function getItemsByIdentifier($menu_identifier) {
		return $this->getItems(ORM::factory('Menu')
						->where('identifier', '=', $menu_identifier)
						->and_where('lng_id', '=', ORM::factory('Language')->getLangId())
						->find()
						->id);
	}
}