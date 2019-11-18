<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Carsales extends Controller_Admin_Application {
	
	public function action_index() {
		if(!ORM::factory('Permission')->checkPermission('carsales')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/carsales/list')
			->bind('filters', $filters)
			->bind('managers', $managers)
			->bind('pagination', $pagination)
			->bind('statuses', $statuses)
			->bind('carsales', $carsales);
			
		$this->template->title = 'Автовыкуп';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$carsaless_orm = ORM::factory('Carsale');
		$carsaless_orm->reset(FALSE);
			
		/*if(!empty($_GET['manager_id'])) {
			$filters['manager_id'] = $_GET['manager_id'];
			$carsaless_orm = $carsaless_orm->and_where('manager_id', '=', $filters['manager_id']);
		}*/
		
		$count = $carsaless_orm->count_all();
		
		$pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
		  'controller' =>  'carsales',
		  'action' =>  'index'
		));
		$carsales = $carsaless_orm->limit($pagination->items_per_page)->offset($pagination->offset)->order_by('date_time', 'desc')->find_all()->as_array();
		
		$this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
		$statuses = array('new' => "Новый",'in_progres' => "В процессе", 'done' => "Готов");
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('carsales')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/carsales');
		
		$this->template->content = View::factory('admin/carsales/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('statuses', $statuses)
			->bind('data', $data);
			
        $this->template->title = 'Подробнее';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$carsales = ORM::factory('Carsale')->where('id', '=', $id)->find();
		$data = $carsales->as_array();;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {				
				$values = array(
					'status'	
				);
				
				$carsales->values($this->request->post(), $values);
				$carsales->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/carsales');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		
		$statuses = array('new' => "Новый",'in_progres' => "В процессе", 'done' => "Готов");
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('carsales')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$carsales = ORM::factory('Carsale')->where('id', '=', $id)->find();
			
			$carsales->delete();
		}
		
		Controller::redirect('admin/carsales');
	}

} // End Admin_User



class Validation_Exception extends Exception {};
