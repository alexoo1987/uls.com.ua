<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Comments extends Controller_Admin_Application {
	
	public function action_index() {
		if(!ORM::factory('Permission')->checkPermission('comments')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/comments/list')
			->bind('filters', $filters)
			->bind('managers', $managers)
			->bind('pagination', $pagination)
			->bind('statuses', $statuses)
			->bind('comments', $comments);
			
		$this->template->title = 'Отзывы и предложения';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$comments_orm = ORM::factory('Comment');
		$comments_orm->reset(FALSE);
		
		$count = $comments_orm->count_all();
		
		$pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
		  'controller' =>  'comments',
		  'action' =>  'index'
		));
		$comments = $comments_orm->limit($pagination->items_per_page)->offset($pagination->offset)->order_by('date_time', 'desc')->find_all()->as_array();
		
		$this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
		$statuses = array('new' => "Новый",'in_progres' => "В процессе", 'done' => "Готов");
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('comments')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/comments');
		
		$this->template->content = View::factory('admin/comments/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('statuses', $statuses)
			->bind('data', $data);
			
        $this->template->title = 'Просмотр отзыва';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$comment = ORM::factory('Comment')->where('id', '=', $id)->find();
		$data = $comment->as_array();
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
                $values = array(
                    'name',
                    'number_order',
                    'order_position',
                    'like',
                    'dis_like',
                    'suggestions',
                    'answer',
                );
                $comment->active = empty($_POST['active']) ? 0 : 1;
                $comment->values($this->request->post(), $values);
                $comment->save();

				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/comments');
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
		if(!ORM::factory('Permission')->checkPermission('comments')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$comment = ORM::factory('Comment')->where('id', '=', $id)->find();
			
			$comment->delete();
		}
		
		Controller::redirect('admin/comments');
	}

} // End Admin_User



class Validation_Exception extends Exception {};
