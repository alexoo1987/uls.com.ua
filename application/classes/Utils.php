<?php defined('SYSPATH') or die('No direct script access.');

class Utils {
	
	public static function order_by($current, $column, $text)
	{
		$new_direction = 'asc';
		if($current['column'] == $column) {
			$new_direction = ($current['direction'] == 'desc' ? 'asc': 'desc');
		}
		$query = URL::query(array('order_by' => $column, 'order_direction' => $new_direction));
		$url = URL::base(true).Request::current()->uri().$query;
		
		$direction_icon = '<i class="'.($current['direction'] == 'desc' ? "icon-arrow-down" : "icon-arrow-up").'"></i>';
		
		return '<a href="'.$url.'">'.$text.''.$direction_icon.'</a>';
	}
}