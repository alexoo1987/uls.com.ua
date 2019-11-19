<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Cars extends Controller_Admin_Application {
	
	public function action_index() {
		$this->template->content = View::factory('admin/cars/list')
			->bind('filters', $filters)
			->bind('managers', $managers)
			->bind('pagination', $pagination)
			->bind('statuses', $statuses)
			->bind('client_id', $id)
			->bind('cars', $cars);
			
		$this->template->title = 'Авто клиента';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		
		$id = $this->request->param('id');
		
		$cars = ORM::factory('Car')->where('client_id', '=', $id)->find_all()->as_array();
		
		$this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
		$this->template->scripts[] = 'common/cars_list';
	}

	public function action_add() {
		$this->template->content = View::factory('admin/cars/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Добавить';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$client_id = $this->request->param('id');
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$car = ORM::factory('Car');
				$car->values($this->request->post(), array(
					'brand',
					'model',
					'vin',
					'engine',
					'year',
				));
				
				$car->client_id = $client_id;
				
				$car->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/cars/index/'.$client_id);
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		$this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
		$this->template->scripts[] = 'bootstrap-formhelpers-phone';
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/cars_form';
	}
	
	public function action_edit() {
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('orders');
		
		$this->template->content = View::factory('admin/cars/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Редактировать';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$car = ORM::factory('Car')->where('id', '=', $id)->find();
		$data = array();
		$data = $car->as_array();
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {				
				$values = array(
					'brand',
					'model',
					'vin',
					'engine',
					'year',	
				);
				$car->values($this->request->post(), $values);
				
				$car->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/cars/index/'.$car->client_id);
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
		$this->template->scripts[] = 'bootstrap-formhelpers-phone';
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/cars_form';
		
	}
	
	public function action_delete() {
		if(!ORM::factory('Client')->logged_in()) {
			return Controller::redirect('authorization/login?order_add=true');
		}
		
		$client = ORM::factory('Client')->get_client();
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$car = ORM::factory('Car')->where('id', '=', $id)->find();
			$client_id = $car->client_id;
			
			$car->delete();
		}
		
		Controller::redirect('admin/cars/index/'.$client_id);
	}
} // End Admin_User



class Validation_Exception extends Exception {};
