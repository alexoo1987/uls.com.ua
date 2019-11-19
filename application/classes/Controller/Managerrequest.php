<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Managerrequest extends Controller_Application {	
	public function action_index()
	{
		$this->template->content = View::factory('managerrequest/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Запрос менеджеру';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$managerrequest = ORM::factory('Managerrequest')->values($this->request->post(), array(
					// 'manufacturer',
					// 'model',
					// 'modification',
					// 'year',
					// 'vin',
					// 'city',
					// 'volume',
					'name',
					'phone',
					'description',
				));
				
				$managerrequest->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				$message = "Запрос отправлен. В ближайшее время с Вами свяжется наш менеджер.";
				
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправьте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/managerrequest_form';
	}
}
