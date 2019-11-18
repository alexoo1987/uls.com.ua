<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Managerrequest extends Controller_Admin_Application {
	
	public function action_index() {
		if(!ORM::factory('Permission')->checkPermission('managerrequest')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/managerrequest/list')
			->bind('filters', $filters)
			->bind('managers', $managers)
			->bind('pagination', $pagination)
			->bind('statuses', $statuses)
			->bind('managerrequests', $managerrequests);
			
		$this->template->title = 'Запросы менеджеру';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$managerrequests_orm = ORM::factory('Managerrequest');
		$managerrequests_orm->reset(FALSE);
			
		/*if(!empty($_GET['manager_id'])) {
			$filters['manager_id'] = $_GET['manager_id'];
			$managerrequests_orm = $managerrequests_orm->and_where('manager_id', '=', $filters['manager_id']);
		}*/
		
		$count = $managerrequests_orm->count_all();
		
		$pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
		  'controller' =>  'managerrequest',
		  'action' =>  'index'
		));
		$managerrequests = $managerrequests_orm->limit($pagination->items_per_page)->offset($pagination->offset)->order_by('date_time', 'desc')->find_all()->as_array();
		
		$this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
		$statuses = array('new' => "Новый",'in_progres' => "В процессе", 'done' => "Готов");
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('managerrequest')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/managerrequest');
		
		$this->template->content = View::factory('admin/managerrequest/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('statuses', $statuses)
			->bind('data', $data);
			
        $this->template->title = 'Подробнее';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$managerrequest = ORM::factory('Managerrequest')->where('id', '=', $id)->find();
		$data = $managerrequest->as_array();;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {				
				$values = array(
					'status'	
				);
				
				$managerrequest->values($this->request->post(), $values);
				$managerrequest->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/managerrequest');
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
		if(!ORM::factory('Permission')->checkPermission('manage_managerrequests')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$managerrequest = ORM::factory('managerrequest')->where('id', '=', $id)->find();
			
			$managerrequest->delete();
		}
		
		Controller::redirect('admin/managerrequests');
	}

} // End Admin_User



class Validation_Exception extends Exception {};
