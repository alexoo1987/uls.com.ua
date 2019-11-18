<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Menu extends Controller_Admin_Application {
	
	public function action_list() {
		if(!Auth::instance()->logged_in('admin')) Controller::redirect('');
		
		$this->template->content = View::factory('admin/menus/list')
			->bind('menus', $menus);
		
		$this->template->title = 'Administrator::Menus';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$menus = ORM::factory('Menu')->find_all()->as_array();
		
		$this->template->scripts[] = "common/menus_list";
	}
	
	public function action_items() {
		if(!Auth::instance()->logged_in('admin')) Controller::redirect('');
		
		$this->template->content = View::factory('admin/menus/list_items')
			->bind('items', $items)
			->bind('menu', $menu);
			
			
		$menu_id = $this->request->param('id');
		$menu = ORM::factory('Menu')->where('id', '=', $menu_id)->find();
		
		$this->template->title = 'Administrator::Menu Items';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$items = ORM::factory('Menuitem')->getItems($menu_id);
		
		$this->template->styles[] = "south-street/jquery-ui-1.10.4.custom.min";
		$this->template->scripts[] = "jquery-ui-1.10.4.custom.min";
		$this->template->scripts[] = "jquery.mjs.nestedSortable";
		$this->template->scripts[] = "common/menus_list_items";
	}
	
	public function action_add() {
		if(!Auth::instance()->logged_in('admin')) Controller::redirect('');
		
		$this->template->content = View::factory('admin/menus/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
		$this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$menu = ORM::factory('Menu');
				$menu->values($this->request->post(), array(
					'name',
					'identifier',
					'max_levels',
				));
				$menu->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/menu/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Fix errors!!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.en';
		$this->template->scripts[] = 'common/menus_form';
	}
	
	public function action_edit() {
		if(!Auth::instance()->logged_in('admin')) Controller::redirect('');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/menus/list');
		
		$this->template->content = View::factory('admin/menus/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$menu = ORM::factory('Menu')->where('id', '=', $id)->find();
		$data = array();
		$data['name'] = $menu->name;
		$data['identifier'] = $menu->identifier;
		$data['max_levels'] = $menu->max_levels;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$menu->values($this->request->post(), array(
					'name',
					'identifier',
					'max_levels',
				));
				$menu->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/menu/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Fix errors!!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.en';
		$this->template->scripts[] = 'common/menus_form';
	}
	
	public function action_delete() {
		if(!Auth::instance()->logged_in('admin')) Controller::redirect('');
		
		$this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$menu = ORM::factory('Menu')->where('id', '=', $id)->find();
			
			$menu->delete();
			
			
			foreach(ORM::factory('Menuitem')->getItems($id) as $item) {
				$item->delete();
			}
		}
		
		Controller::redirect('admin/menu/list');
	}
	
	
	public function action_add_item() {
		if(!Auth::instance()->logged_in('admin')) Controller::redirect('');
		
		$menu_id = $this->request->param('menu_id');
		$parent_id = $this->request->param('parent_id');
		
		$this->template->content = View::factory('admin/menus/form_item')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('pages', $pages)
			->bind('data', $data);
			
		$this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$item = ORM::factory('Menuitem');
				$item->set('name', Arr::get($_POST, 'name', NULL));
				$item->set('link_title', Arr::get($_POST, 'link_title', NULL));
				$item->set('menu_id', $menu_id);
				$item->set('parent_id', $parent_id);
				$item->set('page_id', Arr::get($_POST, 'page_id', NULL));
				$item->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/menu/items/'.$menu_id);
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Fix errors!!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$pages = array(0 => "---");
		
		foreach(ORM::factory('Page')->find_all()->as_array() as $page) {
			$pages[$page->id] = $page->title." (".$page->syn.")";
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.en';
		$this->template->scripts[] = 'common/menus_form_item';
	}
	
	public function action_edit_item() {
		if(!Auth::instance()->logged_in('admin')) Controller::redirect('');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/menus/list');
		
		$this->template->content = View::factory('admin/menus/form_item')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('pages', $pages)
			->bind('data', $data);
			
        $this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$item = ORM::factory('Menuitem')->where('id', '=', $id)->find();
		$data = array();
		$data['name'] = $item->name;
		$data['link_title'] = $item->link_title;
		$data['page_id'] = $item->page_id;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$item->set('name', Arr::get($_POST, 'name', NULL));
				$item->set('link_title', Arr::get($_POST, 'link_title', NULL));
				$item->set('page_id', Arr::get($_POST, 'page_id', NULL));
				$item->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/menu/items/'.$item->menu_id);
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Fix errors!!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$pages = array(0 => "---");
		
		foreach(ORM::factory('Page')->find_all()->as_array() as $page) {
			$pages[$page->id] = $page->title." (".$page->syn.")";
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.en';
		$this->template->scripts[] = 'common/menus_form_item';
	}
	
	public function action_delete_item() {
		if(!Auth::instance()->logged_in('admin')) Controller::redirect('');
		
		$this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$item = ORM::factory('Menuitem')->where('id', '=', $id)->find();
			$menu_id = $item->menu_id;
			$item->delete();
		}
		
		Controller::redirect('admin/menu/items/'.$menu_id);
	}
	
	public function action_save_sorting() {
		if(!Auth::instance()->logged_in('admin')) Controller::redirect('');
		$this->auto_render = FALSE;
		
		$menu_items = array();
		$menu_items = Arr::get($_POST, 'sorted_arr', array());
		
		$order = 1;
		foreach($menu_items as $menu_item) {
			if($menu_item['item_id'] == "") continue;
			if($menu_item['parent_id'] == "") $menu_item['parent_id'] = NULL;
			
			$item = ORM::factory('Menuitem')->where('id', '=', $menu_item['item_id'])->find();
			$item->parent_id = $menu_item['parent_id'];
			$item->order_by = $order;
			$item->save();
		
			$order++;
		}
	}

} // End Admin_User



class Validation_Exception extends Exception {};
