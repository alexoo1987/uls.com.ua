<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Category extends ORM {
    protected $_table_name = 'categories';

    public function get_children($ga_ids=false) {
    	$cats = ORM::factory('Category')->where('level', '=', ($this->level + 1))->and_where('parent_id', '=', $this->id)->order_by('column')->find_all()->as_array();
    	if(!$ga_ids) return $cats;
    	$new_cats = array();
    	foreach($cats as $cat) {
    		if(!empty($cat->tecdoc_ids)) {
				$cat_ga_ids = explode(",", $cat->tecdoc_ids);
				if(!empty(array_intersect($ga_ids, $cat_ga_ids))) {
					$new_cats[] = $cat;
				} else {
					$new_cats[] = $cat->name;

				}
    		}
    	}
    	return $new_cats;
    }

	public function get_parent() {
		if (!$this->parent_id) return null;

		$parent = ORM::factory('Category')->where('id', '=', $this->parent_id)->find();
		if ($parent->level == 1) $parent = ORM::factory('Category')->where('id', '=', $parent->parent_id)->find();

		return $parent;
	}
}