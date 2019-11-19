<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Vacancies extends Controller_Admin_Application {
	
	public function action_index() {
		if(!ORM::factory('Permission')->checkPermission('comments')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/vacancies/list')
			->bind('filters', $filters)
			->bind('managers', $managers)
			->bind('pagination', $pagination)
			->bind('statuses', $statuses)
			->bind('comments', $comments);
			
		$this->template->title = 'Вакансии';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$comments_orm = ORM::factory('Vacancies');
		$comments_orm->reset(FALSE);
		
		$count = $comments_orm->count_all();
		
		$pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
		  'controller' =>  'vacancies',
		  'action' =>  'index'
		));
		$comments = $comments_orm->limit($pagination->items_per_page)->offset($pagination->offset)->order_by('id', 'desc')->find_all()->as_array();
		
		$this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
		$statuses = array('new' => "Новый",'in_progres' => "В процессе", 'done' => "Готов");
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('comments')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/vacancies');
		
		$this->template->content = View::factory('admin/vacancies/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('statuses', $statuses)
			->bind('data', $data);
			
        $this->template->title = 'Просмотр вакансии';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$comment = ORM::factory('Vacancies')->where('id', '=', $id)->find();
		$data = $comment->as_array();
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
                $values = array(
                    'title',
                    'description',
                    'salary',
                    'employment',
                    'experiance',
                    'vaiting_results',
                    'requirements',
                    'working_conditions',
                    'probation',
                    'meta_description',
                );

                $comment->values($this->request->post(), $values);
                $comment->save();

				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/vacancies');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'ckeditor/ckeditor';
        $this->template->scripts[] = 'Djenx.Explorer/djenx-explorer';
        $this->template->scripts[] = 'common/pages_form';
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('comments')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$comment = ORM::factory('Vacancies')->where('id', '=', $id)->find();
			
			$comment->delete();
		}
		
		Controller::redirect('admin/vacancies');
	}
    public function action_create() {
        $this->template->content = View::factory('admin/vacancies/form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('comments', $comments)
            ->bind('pagination', $pagination)
            ->bind('data', $data);

        $this->template->title = 'Вакансии';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $comment = ORM::factory('Vacancies')->values($this->request->post(), array(
                    'title',
                    'description',
                    'salary',
                    'employment',
                    'experiance',
                    'vaiting_results',
                    'requirements',
                    'working_conditions',
                    'probation',
                    'meta_description',
                ));

                $comment->save();

                // Reset values so form is not sticky
                $_POST = array();

                $message = "Вакансия добавлена";

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
        $this->template->scripts[] = 'ckeditor/ckeditor';
        $this->template->scripts[] = 'Djenx.Explorer/djenx-explorer';
        $this->template->scripts[] = 'common/pages_form';

    }


} // End Admin_User



class Validation_Exception extends Exception {};
