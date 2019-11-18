<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cars extends Controller_Application {	
	public function action_add() {
		if(!ORM::factory('Client')->logged_in()) {
			return Controller::redirect('authorization/login?order_add=true');
		}

		$this->template->content = View::factory('cars/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Добавить';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

		$client = ORM::factory('Client')->get_client();
			
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
				
				$car->client_id = $client->id;
				
				$car->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('orders');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		$this->template->scripts[] = 'common/cars_form';
	}
	
	public function action_edit() {
		if(!ORM::factory('Client')->logged_in()) {
			return Controller::redirect('authorization/login?order_add=true');
		}
		
		$client = ORM::factory('Client')->get_client();

		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('orders');
		
		$this->template->content = View::factory('cars/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Редактировать';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$car = ORM::factory('Car')->where('id', '=', $id)->and_where('client_id', '=', $client->id)->find();
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
				
				Controller::redirect('orders');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
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
			$car = ORM::factory('Car')->where('id', '=', $id)->and_where('client_id', '=', $client->id)->find();
			
			$car->delete();
		}
		
		Controller::redirect('orders');
	}
	
}
