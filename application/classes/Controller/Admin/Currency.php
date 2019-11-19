<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Currency extends Controller_Admin_Application {
	
	public function action_list() {
		if(!ORM::factory('Permission')->checkPermission('currency')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/currency/list')
			->bind('currencies', $currencies);
		$this->template->title = 'Currency';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$currencies = ORM::factory('Currency')->find_all()->as_array();
		
		$this->template->scripts[] = "common/currency_list";
	}
	
	public function action_add() {
		if(!ORM::factory('Permission')->checkPermission('currency')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/currency/form')
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
				$currency = ORM::factory('Currency');
				$currency->values($this->request->post(), array(
					'name',
					'ratio'	
				));
				$currency->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/currency/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('currency')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/currency/list');
		
		$this->template->content = View::factory('admin/currency/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Edit';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$currency = ORM::factory('Currency')->where('id', '=', $id)->find();
		$data = array();
		$data['name'] = $currency->name;
		$data['ratio'] = $currency->ratio;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$currency->values($this->request->post(), array(
					'name',
					'ratio'	
				));
				$currency->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/currency/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('currency')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$currency = ORM::factory('Currency')->where('id', '=', $id)->find();
			
			$currency->delete();
		}
		
		Controller::redirect('admin/currency/list');
	}

} // End Admin_User



class Validation_Exception extends Exception {};
