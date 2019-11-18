<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Discount extends Controller_Admin_Application {
	
	public function action_list() {
		if(!ORM::factory('Permission')->checkPermission('discount_manage')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/discount/list')
			->bind('discounts', $discounts);
		$this->template->title = 'discount';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$discounts = ORM::factory('Discount')->order_by('id', 'asc')->find_all()->as_array();
		
		$this->template->scripts[] = "common/discounts_list";
	}
	
	public function action_add() {
		if(!ORM::factory('Permission')->checkPermission('discount_manage')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/discount/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Add';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$discount = ORM::factory('Discount');
				$discount->values($this->request->post(), array(
					'name'
				));
				if(!empty($_POST['standart'])) {
					$standart_discount = ORM::factory('Discount')->where('standart', '=', 1)->find();
					if($standart_discount->id) {
						$standart_discount->standart = 0;
						$standart_discount->save();
					}
					$discount->standart = !empty($_POST['standart']) && $_POST['standart'] == 1 ? 1 : 0;
				}
				if(!empty($_POST['admin_default'])) {
					$standart_discount = ORM::factory('Discount')->where('admin_default', '=', 1)->find();
					if($standart_discount->id) {
						$standart_discount->admin_default = 0;
						$standart_discount->save();
					}
					$discount->admin_default = !empty($_POST['admin_default']) && $_POST['admin_default'] == 1 ? 1 : 0;
				}
				
				$discount->save();
				
				
				foreach($_POST['from'] as $key=>$val) {
					$values = array();
					$values['from'] = $_POST['from'][$key];
					$values['to'] = $_POST['to'][$key];
					$values['percentage'] = $_POST['percentage'][$key];
					
					$values['discount_id'] = $discount->id;
					
					ORM::factory('DiscountLimit')->values($values)->save();
				}
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/discount/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		$this->template->scripts[] = "common/discounts_form";
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('discount_manage')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/discount/list');
		
		$this->template->content = View::factory('admin/discount/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Edit';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$discount = ORM::factory('Discount')->where('id', '=', $id)->find();
		$data = array();
		$data['name'] = $discount->name;
		$data['standart'] = $discount->standart;
		$data['admin_default'] = $discount->admin_default;
		
		$data['from'] = array();
		$data['to'] = array();
		$data['percentage'] = array();
		
		$i = 0;
		foreach($discount->discount_limits->find_all()->as_array() as $dl) {
			$data['from'][$i] = $dl->from;
			$data['to'][$i] = $dl->to;
			$data['percentage'][$i] = $dl->percentage;
			$i++;
		}
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$discount->values($this->request->post(), array(
					'name',
					'price'	
				));
				
				if(!empty($_POST['standart'])) {
					$standart_discount = ORM::factory('Discount')->where('standart', '=', 1)->find();
					if($standart_discount->id) {
						$standart_discount->standart = 0;
						$standart_discount->save();
					}
					$discount->standart = 1;
				} elseif($discount->standart == 1) {
					$discount->standart = 0;
					$discounts = ORM::factory('Discount')->order_by('id', 'asc')->find_all()->as_array();
					$standart_discount = $discounts[0];
					$standart_discount->standart = 1;
					$standart_discount->save();
				}
				
				if(!empty($_POST['admin_default'])) {
					$standart_discount = ORM::factory('Discount')->where('admin_default', '=', 1)->find();
					if($standart_discount->id) {
						$standart_discount->admin_default = 0;
						$standart_discount->save();
					}
					$discount->admin_default = 1;
				} elseif($discount->admin_default == 1) {
					$discount->admin_default = 0;
					$discounts = ORM::factory('Discount')->order_by('id', 'asc')->find_all()->as_array();
					$standart_discount = $discounts[0];
					$standart_discount->admin_default = 1;
					$standart_discount->save();
				}
				
				$discount->save();
				
				foreach(ORM::factory('DiscountLimit')->where('discount_id', '=', $discount->id)->find_all()->as_array() as $dl)
					$dl->delete();
					
				foreach($_POST['from'] as $key=>$val) {
					$values = array();
					$values['from'] = $_POST['from'][$key];
					$values['to'] = $_POST['to'][$key];
					$values['percentage'] = $_POST['percentage'][$key];
					
					$values['discount_id'] = $discount->id;
					
					ORM::factory('DiscountLimit')->values($values)->save();
				}
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/discount/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		$this->template->scripts[] = "common/discounts_form";
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('discount_manage')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$discount = ORM::factory('Discount')->where('id', '=', $id)->find();
			
			$discount->delete();
		}
		
		Controller::redirect('admin/discount/list');
	}

} // End Admin_User



class Validation_Exception extends Exception {};
